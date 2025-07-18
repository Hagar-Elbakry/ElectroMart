<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        
                        Select::make('payment_method')
                            ->required()
                            ->options([
                                'cash' => 'Cash',
                                'credit cards' => 'Credit Cards'
                            ]), 
                        
                        Select::make('payment_status')
                            ->default('pending')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'completed',
                                'failed' => 'Failed'
                            ]),
                            
                        
                        ToggleButtons::make('status')
                            ->default('new')
                            ->inline()
                            ->required()
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'canceled' => 'Canceled'
                            ])
                            ->colors([
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'gray',
                                'delivered' => 'success',
                                'canceled' => 'danger'
                            ])   
                            ->icons([
                                'new' => 'heroicon-o-sparkles',
                                'processing' => 'heroicon-o-arrow-path',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check-circle',
                                'canceled' => 'heroicon-o-x-circle'
                            ]),
                        
                        Select::make('Currency')
                            ->default('egp')
                            ->required()
                            ->options([
                                'egp' => 'EGP',
                                'eur' => 'EUR',
                                'sar' => 'SAR',
                                'usd' => 'USD'
                            ]),
                            
                        Select::make('shipping_method')
                            ->options([
                                'fedex' => 'FedEx',
                                'ups' => 'UPS',
                                'dhl' => 'DHL'
                            ]),
                            
                        Textarea::make('notes') 
                            ->columnSpanFull()    



                    ])->columns(2),
                    
                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([  
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name') 
                                    ->preload()
                                    ->searchable()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) 
                                                        => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn (Set $set, ?string $state) 
                                                        => $set('total_amount', Product::find($state)?->price ?? 0))
                                    ->required()
                                    ->columnSpan(4),
                                
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) 
                                                        => $set('total_amount', $state*$get('unit_amount')))
                                    ->columnSpan(2),
                                
                                TextInput::make('unit_amount') 
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(3),
                                    
                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(3)   

                            ])->columns(12),

                        Placeholder::make('grand_total')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                $repeaters = $get('items');
                                if(!$repeaters) {
                                    return $total;
                                } else {
                                    foreach($repeaters as $repeater => $value) {
                                        $total += $get("items.{$repeater}.total_amount");
                                    }
                                    
                                    $set('grand_total', $total);
                                    return Number::currency($total, 'EGP');
                                }
                            }),
                            
                        Hidden::make('grand_total')
                            ->default(0)   
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('grand_total')
                    ->sortable()
                    ->numeric()
                    ->money('EGP'),

               
                TextColumn::make('payment_method'),
                TextColumn::make('payment_status'),

                TextColumn::make('currency'),

                TextColumn::make('shipping_method'),

                 SelectColumn::make('status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'canceled' => 'Canceled' 
                    ]),    
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'credit cards' => 'Credit Cards'
                    ]),
                
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'completed',
                        'failed' => 'Failed'
                    ]),
                    
                SelectFilter::make('currency') 
                    ->options([
                        'egp' => 'EGP',
                        'eur' => 'EUR',
                        'sar' => 'SAR',
                        'usd' => 'USD'
                    ]),
                
                SelectFilter::make('shipping_method')   
                    ->options([
                        'fedex' => 'FedEx',
                        'ups' => 'UPS',
                        'dhl' => 'DHL'
                    ])   
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string {
        return 'The number of orders';
    }

    public static function getNavigationBadgeColor(): ?string {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
