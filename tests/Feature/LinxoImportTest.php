<?php

namespace Tests\Feature;

use App\Filament\Resources\BankTransactions\Pages\ManageBankTransactions;
use App\Models\BankTransaction;
use App\Models\User;
use App\Services\LinxoImporter;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class LinxoImportTest extends TestCase
{
    use RefreshDatabase;

    /** Reproduit le format de l'export Linxo : UTF-16LE + BOM, tabulations, CRLF. Données fictives. */
    private function linxoExport(array $rows): string
    {
        $lines = ["Date\tLibellé\tCatégorie\tMontant\tNotes\tN° de chèque\tLabels\tNom du compte\tNom de la connexion"];

        foreach ($rows as $row) {
            $lines[] = implode("\t", $row);
        }

        return "\xFF\xFE" . mb_convert_encoding(implode("\r\n", $lines) . "\r\n", 'UTF-16LE', 'UTF-8');
    }

    private function sample(): string
    {
        return $this->linxoExport([
            ['17/11/2026', 'BOULANGERIE TEST', 'Alimentation, supermarché', '-7,12', 'CARTE 16/11 BOULANGERIE', '', '', 'Compte courant', 'Banque A'],
            ['16/11/2026', 'EN VOTRE FAVEUR Site vitrine client', 'Virements à catégoriser', '420', '', '', '', 'Compte courant', 'Banque A'],
            ['16/11/2026', 'VIR INST vers moi', 'Virements internes', '-150', '', '', '', 'Compte courant', 'Banque A'],
            // deux opérations identiques le même jour : toutes deux réelles
            ['12/11/2026', 'PEAGE A4', 'Parking, péages, PV', '-7,5', '', '', '', 'Compte courant', 'Banque A'],
            ['12/11/2026', 'PEAGE A4', 'Parking, péages, PV', '-7,5', '', '', '', 'Compte courant', 'Banque A'],
            ['10/11/2026', 'NOTE "bizarre', 'Shopping/e-Commerce', '-1 234,56', 'guillemet "isolé', '', '', 'Livret', 'Banque B'],
        ]);
    }

    public function test_importe_un_export_linxo_utf16(): void
    {
        $result = app(LinxoImporter::class)->import($this->sample());

        $this->assertSame(['created' => 6, 'skipped' => 0], $result);

        $cb = BankTransaction::where('label', 'BOULANGERIE TEST')->sole();
        $this->assertSame('2026-11-17', $cb->date->toDateString());
        $this->assertSame('-7.12', $cb->amount);
        $this->assertSame('Alimentation, supermarché', $cb->category);
        $this->assertSame('Banque A', $cb->connection);
        $this->assertFalse($cb->is_internal);

        $this->assertTrue(BankTransaction::where('label', 'VIR INST vers moi')->sole()->is_internal);
        $this->assertSame('-1234.56', BankTransaction::where('connection', 'Banque B')->sole()->amount);
        $this->assertSame(2, BankTransaction::where('label', 'PEAGE A4')->count());
    }

    public function test_reimporter_n_ajoute_pas_de_doublons(): void
    {
        $importer = app(LinxoImporter::class);
        $importer->import($this->sample());

        $this->assertSame(['created' => 0, 'skipped' => 6], $importer->import($this->sample()));

        $withNewOne = $this->linxoExport([
            ['18/11/2026', 'NOUVELLE OPE', 'Revenus', '10', '', '', '', 'Compte courant', 'Banque A'],
            ['12/11/2026', 'PEAGE A4', 'Parking, péages, PV', '-7,5', '', '', '', 'Compte courant', 'Banque A'],
        ]);

        $this->assertSame(['created' => 1, 'skipped' => 1], $importer->import($withNewOne));
        $this->assertSame(7, BankTransaction::count());
    }

    public function test_rejette_un_fichier_qui_n_est_pas_un_export_linxo(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(LinxoImporter::class)->import("nom;prenom\nDupont;Jean\n");
    }

    public function test_la_page_comptabilite_s_affiche_avec_les_totaux(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());
        app(LinxoImporter::class)->import($this->sample());
        BankTransaction::where('label', 'like', '%Site vitrine%')->update(['is_professional' => true]);

        $this->get('/admin/comptabilite')
            ->assertOk()
            ->assertSee('Importer un export Linxo')
            ->assertSee('BOULANGERIE TEST')
            ->assertDontSee('VIR INST vers moi') // virements internes exclus par défaut
            ->assertSee('CA pro (Lovaweb)');
    }

    public function test_le_bouton_d_import_ne_laisse_aucun_fichier_sur_le_disque(): void
    {
        Storage::fake('local');
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());

        Livewire::test(ManageBankTransactions::class)
            ->callAction('importLinxo', data: [
                'file' => UploadedFile::fake()->createWithContent('operations.csv', $this->sample()),
            ])
            ->assertHasNoActionErrors()
            ->assertNotified('6 opération(s) importée(s)');

        $this->assertSame(6, BankTransaction::count());
        $this->assertSame([], Storage::disk('local')->allFiles(), 'Le CSV ne doit pas rester sur le disque.');
    }

    public function test_le_bouton_tout_vider_supprime_toutes_les_operations(): void
    {
        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create());
        app(LinxoImporter::class)->import($this->sample());

        Livewire::test(ManageBankTransactions::class)
            ->callAction('resetAll')
            ->assertNotified('Comptabilité vidée');

        $this->assertSame(0, BankTransaction::count());

        // on peut réimporter depuis zéro
        $this->assertSame(['created' => 6, 'skipped' => 0], app(LinxoImporter::class)->import($this->sample()));
    }
}
