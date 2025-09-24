<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Loja')
                            ->relationship('user', 'name')
                            ->options(function () {
                                if (auth()->user()->role === 'admin') {
                                    return User::where('role', 'store')->pluck('name', 'id');
                                }
                                return User::where('id', auth()->id())->pluck('name', 'id');
                            })
                            ->required()
                            ->default(fn () => auth()->user()->role !== 'admin' ? auth()->id() : null)
                            ->disabled(fn () => auth()->user()->role !== 'admin')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(Client::class, 'email', ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('celphone')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('(11) 99999-9999'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->label('Confirmar Senha')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(false),
                    ])->columns(2),
                
                Forms\Components\Section::make('Endereço')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Endereço')
                            ->maxLength(255)
                            ->placeholder('Rua, Avenida, etc.'),
                        
                        Forms\Components\TextInput::make('number')
                            ->label('Número')
                            ->maxLength(10)
                            ->placeholder('123'),
                        
                        Forms\Components\TextInput::make('reference_point')
                            ->label('Ponto de Referência')
                            ->maxLength(255)
                            ->placeholder('Próximo ao shopping, etc.'),
                        
                        Forms\Components\TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(100)
                            ->placeholder('São Paulo'),
                        
                        Forms\Components\TextInput::make('state')
                            ->label('Estado')
                            ->maxLength(2)
                            ->placeholder('SP')
                            ->helperText('Use a sigla do estado (ex: SP, RJ, MG)'),
                        
                        Forms\Components\TextInput::make('country')
                            ->label('País')
                            ->maxLength(100)
                            ->default('Brasil')
                            ->placeholder('Brasil'),
                        
                        Forms\Components\TextInput::make('zipcode')
                            ->label('CEP')
                            ->maxLength(10)
                            ->placeholder('12345-678')
                            ->mask('99999-999'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Redes Sociais')
                    ->schema([
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->maxLength(255)
                            ->placeholder('@usuario ou URL completa')
                            ->prefix('@')
                            ->suffixIcon('heroicon-m-at-symbol'),
                        
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook')
                            ->maxLength(255)
                            ->placeholder('URL do Facebook')
                            ->url()
                            ->suffixIcon('heroicon-m-link'),
                        
                        Forms\Components\TextInput::make('site')
                            ->label('Site')
                            ->maxLength(255)
                            ->placeholder('https://www.seusite.com.br')
                            ->url()
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ])->columns(3),
                
                Forms\Components\Section::make('Imagem')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Foto do Cliente')
                            ->image()
                            ->directory('clients')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                            ])
                            ->maxSize(2048)
                            ->helperText('Tamanho máximo: 2MB. Formatos aceitos: JPG, PNG, GIF'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/default-avatar.png'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('celphone')
                    ->label('Celular')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Loja')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('user')
                    ->label('Loja')
                    ->relationship('user', 'name')
                    ->visible(fn () => auth()->user()->role === 'admin'),
                
                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options([
                        'AC' => 'Acre',
                        'AL' => 'Alagoas',
                        'AP' => 'Amapá',
                        'AM' => 'Amazonas',
                        'BA' => 'Bahia',
                        'CE' => 'Ceará',
                        'DF' => 'Distrito Federal',
                        'ES' => 'Espírito Santo',
                        'GO' => 'Goiás',
                        'MA' => 'Maranhão',
                        'MT' => 'Mato Grosso',
                        'MS' => 'Mato Grosso do Sul',
                        'MG' => 'Minas Gerais',
                        'PA' => 'Pará',
                        'PB' => 'Paraíba',
                        'PR' => 'Paraná',
                        'PE' => 'Pernambuco',
                        'PI' => 'Piauí',
                        'RJ' => 'Rio de Janeiro',
                        'RN' => 'Rio Grande do Norte',
                        'RS' => 'Rio Grande do Sul',
                        'RO' => 'Rondônia',
                        'RR' => 'Roraima',
                        'SC' => 'Santa Catarina',
                        'SP' => 'São Paulo',
                        'SE' => 'Sergipe',
                        'TO' => 'Tocantins',
                    ])
                    ->searchable(),
                
                Tables\Filters\Filter::make('has_social_media')
                    ->label('Com Redes Sociais')
                    ->query(fn (Builder $query): Builder => $query->where(function ($query) {
                        $query->whereNotNull('instagram')
                              ->orWhereNotNull('facebook')
                              ->orWhereNotNull('site');
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Se não for admin, mostrar apenas clientes da própria loja
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        return $query;
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}