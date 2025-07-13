<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Grid::make()
                        ->schema([
                            TextInput::make('name')
                                    ->maxLength(255)
                                    ->live(onBlur:true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) 
                                                        => $set('slug', Str::slug($state)))
                                    ->required(),

                            TextInput::make('slug')   
                                    ->maxLength(255)
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Category::class, 'slug', ignoreRecord:true)      
                        ]),

                        FileUpload::make('image')
                                    ->image()
                                    ->directory('categories'),
                        
                        Toggle::make('is_active')
                                    ->required()
                                    ->offColor('danger')
                                    ->onColor('success')
                                    ->default(true)    
                ])
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('image'),
                TextColumn::make('slug'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('updated_at')
                    ->since()
                    ->dateTimeTooltip()   

            ])
            ->filters([
                Filter::make('is_active')
                    ->toggle()
                    ->label('Active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
