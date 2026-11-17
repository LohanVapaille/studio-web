<?php

namespace App\Filament\Resources\BankTransactions\Pages;

use App\Filament\Resources\BankTransactions\BankTransactionResource;
use App\Filament\Resources\BankTransactions\Widgets\BankOverview;
use App\Models\BankTransaction;
use App\Services\LinxoImporter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageBankTransactions extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = BankTransactionResource::class;

    protected ?string $heading = 'Comptabilité';

    protected ?string $subheading = 'Totaux calculés sur les filtres du tableau, hors virements internes.';

    protected function getHeaderWidgets(): array
    {
        return [BankOverview::class];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importLinxo')
                ->label('Importer un export Linxo')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Importer un export Linxo')
                ->modalDescription('Dans Linxo : Historique → exporter. Les opérations déjà importées sont ignorées, tu peux réimporter un export qui chevauche le précédent.')
                ->modalSubmitActionLabel('Importer')
                ->schema([
                    // Le CSV n'est jamais copié : on lit le fichier temporaire de Livewire
                    // (disque privé, cf. AppServiceProvider) puis on le supprime aussitôt.
                    FileUpload::make('file')
                        ->label('Fichier operations.csv')
                        ->storeFiles(false)
                        ->maxSize(10240)
                        ->required(),
                ])
                ->action(function (array $data, LinxoImporter $importer) {
                    /** @var TemporaryUploadedFile $file */
                    $file = $data['file'];

                    try {
                        $result = $importer->import($file->get());
                    } catch (InvalidArgumentException $e) {
                        Notification::make()->title('Import impossible')->body($e->getMessage())->danger()->send();

                        return;
                    } finally {
                        $file->delete();
                    }

                    Notification::make()
                        ->title($result['created'] . ' opération(s) importée(s)')
                        ->body($result['skipped'] ? $result['skipped'] . ' déjà présente(s), ignorée(s).' : null)
                        ->success()
                        ->send();
                }),

            Action::make('resetAll')
                ->label('Tout vider')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->outlined()
                ->visible(fn () => BankTransaction::exists())
                ->requiresConfirmation()
                ->modalHeading('Vider toute la comptabilité ?')
                ->modalDescription(fn () => 'Les ' . BankTransaction::count() . ' opérations seront supprimées définitivement, y compris les marquages « pro » et « interne ». Tu pourras ensuite réimporter un export Linxo depuis zéro.')
                ->modalSubmitActionLabel('Oui, tout supprimer')
                ->action(function () {
                    $deleted = BankTransaction::query()->delete();

                    Notification::make()
                        ->title('Comptabilité vidée')
                        ->body($deleted . ' opération(s) supprimée(s).')
                        ->success()
                        ->send();
                }),
        ];
    }
}
