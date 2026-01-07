<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Purchase;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Celkové tržby', number_format(Purchase::where('status', 'completed')->sum('amount'), 2, ',', ' ') . ' €')
                ->description('Všetky prijaté platby')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success'),
            Stat::make('Tržby tento mesiac', number_format(Purchase::where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'), 2, ',', ' ') . ' €')
                ->description('Platby za aktuálny mesiac')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Registrovaní študenti', User::count())
                ->description('Celkový počet používateľov')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Nahraté kurzy', Course::count())
                ->description('Celkový počet kurzov')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Aktívne zápisy', Enrollment::count())
                ->description('Celkový počet zápisov')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
            Stat::make('Podozrivé aktivity (24h)', \App\Models\SuspiciousActivity::where('created_at', '>=', now()->subDay())->count())
                ->description('Detegované hrozby')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color(\App\Models\SuspiciousActivity::where('created_at', '>=', now()->subDay())->exists() ? 'danger' : 'success'),
        ];
    }
}
