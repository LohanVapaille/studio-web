<?php

namespace App\Services;

use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Import de l'export « Historique » de Linxo (operations.csv).
 *
 * Format observé : UTF-16LE avec BOM, colonnes séparées par des tabulations,
 * dates JJ/MM/AAAA, montants avec virgule décimale. Les variantes UTF-8 / Windows-1252
 * et séparateurs « ; » ou « , » sont aussi acceptées.
 *
 * Anti-doublon : chaque ligne reçoit une empreinte (date, libellé, montant, compte, notes
 * + rang d'apparition dans le fichier). Réimporter un export qui chevauche le précédent
 * n'ajoute que les nouvelles opérations, tout en gardant deux opérations identiques
 * réellement passées le même jour.
 */
class LinxoImporter
{
    /** En-tête normalisé (minuscules, sans accents) => champ. */
    private const COLUMNS = [
        'date' => 'date',
        'libelle' => 'label',
        'categorie' => 'category',
        'montant' => 'amount',
        'notes' => 'notes',
        'nom du compte' => 'account',
        'nom de la connexion' => 'connection',
    ];

    private const REQUIRED = ['date', 'label', 'amount'];

    /**
     * @return array{created: int, skipped: int}
     */
    public function importFile(string $path): array
    {
        $raw = @file_get_contents($path);

        if ($raw === false) {
            throw new InvalidArgumentException('Impossible de lire le fichier.');
        }

        return $this->import($raw);
    }

    /**
     * @return array{created: int, skipped: int}
     */
    public function import(string $raw): array
    {
        $rows = $this->parse($raw);

        $existing = BankTransaction::whereIn('import_hash', array_column($rows, 'import_hash'))
            ->pluck('import_hash')
            ->flip();

        $new = array_values(array_filter($rows, fn (array $row) => ! isset($existing[$row['import_hash']])));
        $now = now();

        DB::transaction(function () use ($new, $now) {
            foreach (array_chunk($new, 200) as $chunk) {
                BankTransaction::insert(array_map(fn (array $row) => $row + [
                    'is_professional' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk));
            }
        });

        return ['created' => count($new), 'skipped' => count($rows) - count($new)];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function parse(string $raw): array
    {
        $lines = preg_split('/\r\n|\n|\r/', $this->toUtf8($raw));
        $lines = array_values(array_filter($lines, fn (string $line) => trim($line) !== ''));

        if (count($lines) < 1) {
            throw new InvalidArgumentException('Le fichier est vide.');
        }

        $delimiter = $this->detectDelimiter($lines[0]);
        $map = $this->mapHeader($this->split($lines[0], $delimiter));

        $rows = [];
        $occurrences = [];

        foreach (array_slice($lines, 1) as $i => $line) {
            $cells = $this->split($line, $delimiter);
            $get = fn (string $field) => isset($map[$field]) ? trim($cells[$map[$field]] ?? '') : '';

            $date = $this->parseDate($get('date'), $i + 2);
            $amount = $this->parseAmount($get('amount'), $i + 2);
            $label = Str::limit($get('label'), 250, '');
            $category = $get('category') ?: null;
            $notes = $get('notes') ?: null;
            $account = $get('account') ?: null;

            $key = implode('|', [$date, $label, $amount, $account, $notes]);
            $occurrences[$key] = ($occurrences[$key] ?? 0) + 1;

            $rows[] = [
                'date' => $date,
                'label' => $label,
                'category' => $category,
                'amount' => $amount,
                'notes' => $notes,
                'account' => $account,
                'connection' => $get('connection') ?: null,
                'is_internal' => BankTransaction::isInternalCategory($category),
                'import_hash' => sha1($key . '|' . $occurrences[$key]),
            ];
        }

        return $rows;
    }

    private function toUtf8(string $raw): string
    {
        return match (true) {
            str_starts_with($raw, "\xFF\xFE") => mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16LE'),
            str_starts_with($raw, "\xFE\xFF") => mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16BE'),
            str_starts_with($raw, "\xEF\xBB\xBF") => substr($raw, 3),
            mb_check_encoding($raw, 'UTF-8') => $raw,
            default => mb_convert_encoding($raw, 'UTF-8', 'Windows-1252'),
        };
    }

    private function detectDelimiter(string $header): string
    {
        foreach (["\t", ';', ','] as $delimiter) {
            if (str_contains($header, $delimiter)) {
                return $delimiter;
            }
        }

        throw new InvalidArgumentException('Séparateur de colonnes introuvable : ce n\'est pas un export Linxo.');
    }

    /** @return list<string> */
    private function split(string $line, string $delimiter): array
    {
        // Les notes Linxo peuvent contenir des guillemets isolés : en tabulations, on découpe sans interpréter.
        return $delimiter === "\t" ? explode("\t", $line) : str_getcsv($line, $delimiter, '"', '');
    }

    /** @return array<string, int> champ => index de colonne */
    private function mapHeader(array $header): array
    {
        $map = [];

        foreach ($header as $index => $name) {
            $normalized = Str::of(Str::ascii(trim($name)))->lower()->squish()->toString();

            if (isset(self::COLUMNS[$normalized])) {
                $map[self::COLUMNS[$normalized]] = $index;
            }
        }

        $missing = array_diff(self::REQUIRED, array_keys($map));

        if ($missing) {
            throw new InvalidArgumentException('Colonnes manquantes (Date, Libellé, Montant) : ce n\'est pas un export Linxo.');
        }

        return $map;
    }

    private function parseDate(string $value, int $line): string
    {
        try {
            return Carbon::createFromFormat('!d/m/Y', $value)->toDateString();
        } catch (\Throwable) {
            throw new InvalidArgumentException("Date invalide ligne {$line} : « {$value} ».");
        }
    }

    private function parseAmount(string $value, int $line): string
    {
        $clean = str_replace([' ', "\u{00A0}", "\u{202F}", '€'], '', $value);
        $clean = str_replace(',', '.', $clean);

        if (! is_numeric($clean)) {
            throw new InvalidArgumentException("Montant invalide ligne {$line} : « {$value} ».");
        }

        return number_format((float) $clean, 2, '.', '');
    }
}
