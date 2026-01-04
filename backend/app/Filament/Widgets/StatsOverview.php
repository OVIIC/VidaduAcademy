<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Registered Students', User::count())
                ->description('Total users on platform')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Uploaded Courses', Course::count())
                ->description('Total courses avaliable')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Assigned Courses', Enrollment::count())
                ->description('Total active enrollments')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
        ];
    }
}
