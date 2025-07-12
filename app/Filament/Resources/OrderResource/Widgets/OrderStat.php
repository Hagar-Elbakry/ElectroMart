<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Illuminate\Support\Number;

class OrderStat extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::where('status', 'new')->count()),
            Stat::make('Order Processing', Order::where('status', 'processing')->count()),
            Stat::make('Order Shipped', Order::where('status', 'shipped')->count()),
            Stat::make('Average Price', Number::currency(Order::avg('grand_total'), 'EGP'))
        ];
    }
}
