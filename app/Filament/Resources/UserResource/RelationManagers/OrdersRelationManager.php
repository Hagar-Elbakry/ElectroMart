<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\Orders\OrderStatus;
use App\Enums\Orders\PaymentStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('grand_total')
                    ->numeric()
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => OrderStatus::from($state)->color())
                    ->icon(fn(string $state) => OrderStatus::from($state)->icon()),

                TextColumn::make('payment_method'),
                TextColumn::make('payment_status')
                            ->badge()
                            ->color(fn(string $state) => PaymentStatus::from($state)->color()),
                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->since()
                    ->timeTooltip()

            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->actions([
                Action::make('View Order')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
