<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $pluralModelLabel = 'Produtos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuário')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->disabled(fn () => auth()->user()->role !== 'admin'),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->options(function () {
                                if (auth()->user()->role === 'admin') {
                                    return Category::pluck('name', 'id');
                                }
                                return Category::where('user_id', auth()->id())->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('bar_code')
                            ->label('Código de Barras')
                            ->required()
                            ->unique(Product::class, 'bar_code', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Descrição')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Preço e Estoque')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Preço')
                            ->required()
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01)
                            ->minValue(0),

                        Forms\Components\TextInput::make('stock')
                            ->label('Estoque')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Imagens')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Imagens')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->visibility('public')
                            ->reorderable()
                            ->maxFiles(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Imagem')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        if (!$record->images || empty($record->images)) {
                            return null;
                        }
                        
                        $firstImage = is_array($record->images) ? $record->images[0] : $record->images;
                        
                        // Se for uma URL externa, retorna diretamente
                        if (str_starts_with($firstImage, 'http://') || str_starts_with($firstImage, 'https://')) {
                            return $firstImage;
                        }
                        // Se for um arquivo local, usa o storage
                        return asset('storage/' . $firstImage);
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bar_code')
                    ->label('Código de Barras')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Estoque')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state == 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('user')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->visible(fn () => auth()->user()->role === 'admin'),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Estoque Baixo')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '<=', 5)),

                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Sem Estoque')
                    ->query(fn (Builder $query): Builder => $query->where('stock', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Se não for admin, mostrar apenas os produtos do usuário logado
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}