<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
                ->defaultPaginationPageOption(5)
                ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('grand_total')
                    ->numeric()
                    ->money('EGP')
                    ->sortable(),
                
                TextColumn::make('status')    
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'gray',
                                'delivered' => 'success',
                                'canceled' => 'danger'
                            })
                    ->icon(fn(string $state): string => match ($state) {
                                'new' => 'heroicon-o-sparkles',
                                'processing' => 'heroicon-o-arrow-path',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check-circle',
                                'canceled' => 'heroicon-o-x-circle'
                            }),
                            
                TextColumn::make('payment_method'),
                TextColumn::make('payment_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'completed' => 'success',
                                'failed' => 'danger'
                            }),
                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->since()
                    ->timeTooltip()       
            ])
            ->actions([
                Action::make('View Order')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-o-eye')   
            ]);     
    }
}
