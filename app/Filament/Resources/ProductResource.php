<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Dom\Text;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.resources.products.pages.view-product';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state)
                                                        => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord:true),

                        MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')
                    ])->columns(2),

                    Section::make('Images')
                        //->hidden(fn ($get) => empty($get('images')))
                        ->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->reorderable()
                            ->directory('products')
                            ->maxFiles(5)
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Price')->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('EGP')
                    ]),

                    Section::make('Associations')->schema([
                        Select::make('category_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name'),
                        Select::make('brand_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name')
                    ]),

                    Section::make('Status')->schema([
                        Toggle::make('in_stock')
                            ->required()
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        Toggle::make('is_active')
                            ->required()
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->onColor('success')
                            ->offColor('danger')
                            ->required(),


                        Toggle::make('on_sale')
                            ->onColor('success')
                            ->offColor('danger')
                            ->required()
                    ])
                ])->columns(1)
            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make()->schema([
                    \Filament\Infolists\Components\Section::make('Product Information')->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug'),

                        TextEntry::make('description')->markdown()->columnSpanFull()
                    ])->columns(2),
                    \Filament\Infolists\Components\Section::make('Images')
                        ->hidden(fn ($record) => empty($record->images))
                        ->schema([
                        ImageEntry::make('images')
                            ->limit(3)
                            ->limitedRemainingText()
                            ->width(200)
                    ])
                ]),
                Grid::make()->schema([
                    \Filament\Infolists\Components\Section::make('Price')->schema([
                        TextEntry::make('price')
                            ->numeric()
                            ->Money('EGP')
                    ]),
                    \Filament\Infolists\Components\Section::make('Associations')->schema([
                        TextEntry::make('category.name'),
                        TextEntry::make('brand.name'),
                    ]),
                    \Filament\Infolists\Components\Section::make('Status')->schema([
                        Grid::make()->schema([
                            IconEntry::make('in_stock')->boolean(),

                            IconEntry::make('is_active')->boolean(),

                            IconEntry::make('is_featured')->boolean(),
                            IconEntry::make('on_sale')->boolean()
                        ])
                    ])
                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->searchable(),

                TextColumn::make('brand.name')
                    ->searchable(),

                TextColumn::make('price')
                    ->money('EGP')
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),

                IconColumn::make('on_sale')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),

                IconColumn::make('in_stock')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),

                IconColumn::make('is_active')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean()

            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),

                Filter::make('is_featured')
                    ->toggle()
                    ->label('Featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),

                Filter::make('on_sale')
                    ->toggle()
                    ->translateLabel()
                    ->query(fn (Builder $query): Builder => $query->where('on_sale', true)),

                Filter::make('in_stock')
                    ->toggle()
                    ->translateLabel()
                    ->query(fn (Builder $query): Builder => $query->where('in_stock', true)),

                Filter::make('is_active')
                    ->toggle()
                    ->label('Active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
