<?php

namespace MyPlatform\EcommerceCore\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\Visit;
use MyPlatform\EcommerceCore\Modules\Analytics\Models\Conversion;
use MyPlatform\EcommerceCore\Modules\Order\Models\Order;

class AnalyticsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $visitCount = Visit::count();
        $orderCount = Order::where('status', 'paid')->count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        
        $conversionRate = $visitCount > 0 ? ($orderCount / $visitCount) * 100 : 0;

        return [
            Stat::make('Total Visits', $visitCount)
                ->description('Total page views')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Conversion Rate', number_format($conversionRate, 2) . '%')
                ->description('Visits to Paid Orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Revenue', 'SAR ' . number_format($totalRevenue, 2))
                ->description('Total from paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
