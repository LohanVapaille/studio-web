<?php

namespace App\Filament\Resources\BankTransactions;

use App\Filament\Resources\BankTransactions\Pages\ManageBankTransactions;
use App\Models\BankTransaction;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Comptabilité';

    protected static ?string $modelLabel = 'opération';

    protected static ?string $pluralModelLabel = 'opérations';

    protected static ?string $navigationLabel = 'Opérations bancaires';

    protected static ?string $slug = 'comptabilite';

    public static function canCreate(): bool
    {
        return false; // les opérations arrivent par l'import Linxo
    }

    public static function formatEuros(float|string|null $amount, bool $signed = false): string
    {
        $value = (float) $amount;
        $formatted = number_format(abs($value), 2, ',', "\u{202F}") . "\u{00A0}€";

        return match (true) {
            $value < 0 => '−' . $formatted,
            $signed && $value > 0 => '+' . $formatted,
            default => $formatted,
        };
    }

    /** @return array<string, string> */
    private static function distinctOptions(string $column): array
    {
        return BankTransaction::query()
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column, $column)
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->paginated([25, 50, 100])
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(['label', 'notes'])
                    ->wrap()
                    ->extraCellAttributes(['style' => 'min-width: 16rem'])
                    ->description(fn (BankTransaction $record) => $record->notes && $record->notes !== $record->label
                        ? \Illuminate\Support\Str::limit($record->notes, 70)
                        : null),

                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (BankTransaction $record) => $record->is_internal ? 'gray' : 'info')
                    ->toggleable(),

                TextColumn::make('connection')
                    ->label('Banque')
                    ->description(fn (BankTransaction $record) => $record->account)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->alignEnd()
                    ->sortable()
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => self::formatEuros($state, signed: true))
                    ->color(fn ($state) => (float) $state < 0 ? 'danger' : 'success'),

                ToggleColumn::make('is_professional')
                    ->label('Pro')
                    ->tooltip('Liée à l\'activité Lovaweb (compte dans le CA / les dépenses pro)')
                    ->alignCenter(),

                ToggleColumn::make('is_internal')
                    ->label('Interne')
                    ->tooltip('Virement entre mes comptes : exclu des totaux')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('periode')
                    ->label('Période')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '<=', $date)))
                    ->indicateUsing(function (array $data): ?string {
                        $from = ($data['from'] ?? null) ? \Carbon\Carbon::parse($data['from'])->format('d/m/Y') : null;
                        $until = ($data['until'] ?? null) ? \Carbon\Carbon::parse($data['until'])->format('d/m/Y') : null;

                        return match (true) {
                            $from && $until => "Du {$from} au {$until}",
                            (bool) $from => "Depuis le {$from}",
                            (bool) $until => "Jusqu'au {$until}",
                            default => null,
                        };
                    }),

                SelectFilter::make('connection')
                    ->label('Banque')
                    ->options(fn () => self::distinctOptions('connection')),

                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->multiple()
                    ->options(fn () => self::distinctOptions('category')),

                TernaryFilter::make('is_professional')
                    ->label('Activité pro')
                    ->trueLabel('Pro uniquement')
                    ->falseLabel('Perso uniquement'),

                TernaryFilter::make('is_internal')
                    ->label('Virements internes')
                    ->trueLabel('Uniquement')
                    ->falseLabel('Exclus')
                    ->default(false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markPro')
                        ->label('Marquer « pro »')
                        ->icon('heroicon-o-briefcase')
                        ->action(fn (Collection $records) => $records->each->update(['is_professional' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markPerso')
                        ->label('Marquer « perso »')
                        ->icon('heroicon-o-user')
                        ->action(fn (Collection $records) => $records->each->update(['is_professional' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBankTransactions::route('/'),
        ];
    }
}
