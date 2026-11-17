<?php

namespace App\Filament\Resources\BankTransactions\Widgets;

use App\Filament\Resources\BankTransactions\BankTransactionResource;
use App\Filament\Resources\BankTransactions\Pages\ManageBankTransactions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Totaux du tableau des opérations : suit les filtres (période, banque, catégorie…).
 * Rangé dans la ressource pour ne pas apparaître sur le tableau de bord.
 */
class BankOverview extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected function getTablePage(): string
    {
        return ManageBankTransactions::class;
    }

    protected function getStats(): array
    {
        $query = fn () => $this->getPageTableQuery()->reorder();

        $income = (float) $query()->where('amount', '>', 0)->sum('amount');
        $expenses = (float) $query()->where('amount', '<', 0)->sum('amount');
        $proIncome = (float) $query()->where('amount', '>', 0)->where('is_professional', true)->sum('amount');
        $proExpenses = (float) $query()->where('amount', '<', 0)->where('is_professional', true)->sum('amount');
        $count = $query()->count();
        $balance = $income + $expenses;
        $eur = fn (float $v, bool $signed = false) => BankTransactionResource::formatEuros($v, $signed);

        return [
            Stat::make('Entrées', $eur($income))
                ->description($count . ' opération(s)')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Sorties', $eur($expenses))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Solde de la période', $eur($balance, signed: true))
                ->icon('heroicon-o-scale')
                ->color($balance >= 0 ? 'success' : 'danger'),

            Stat::make('CA pro (Lovaweb)', $eur($proIncome))
                ->description('Dépenses pro : ' . $eur($proExpenses))
                ->icon('heroicon-o-briefcase')
                ->color('primary'),
        ];
    }
}
