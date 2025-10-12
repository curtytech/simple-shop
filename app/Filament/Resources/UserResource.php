<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Usuários';
    
    protected static ?string $modelLabel = 'Usuário';
    
    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('Usado para URLs amigáveis'),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email', ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('celphone')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('(11) 99999-9999'),
                        
                        Forms\Components\Textarea::make('slogan')
                            ->label('Slogan')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Descreva o slogan da sua loja'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Função')
                            ->options([
                                'admin' => 'Administrador',
                                'store' => 'Loja',
                            ])
                            ->required()
                            ->default('store'),
                        
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
                        
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->helperText('Deixe vazio se o e-mail não foi verificado'),
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
                
                Forms\Components\Section::make('Imagens')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('users/logos')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                                '16:9',
                            ])
                            ->maxSize(2048)
                            ->helperText('Tamanho máximo: 2MB. Formatos aceitos: JPG, PNG, GIF'),
                        
                        FileUpload::make('banner')
                            ->label('Banner')
                            ->image()
                            ->directory('users/banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '3:1',
                            ])
                            ->maxSize(5120)
                             ->helperText('Tamanho máximo: 5MB. Recomendado: formato panorâmico'),
                     ])->columns(2),
                
                Forms\Components\Section::make('Mercado Pago')
                    ->schema([
                        Forms\Components\TextInput::make('mp_public_key')
                            ->label('Public Key')
                            ->maxLength(255)
                            ->helperText('Chave pública do Mercado Pago para o Checkout'),
                        Forms\Components\TextInput::make('mp_access_token')
                            ->label('Access Token')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('Token de acesso (secreto) da sua conta Mercado Pago'),
                        Forms\Components\Toggle::make('mp_sandbox')
                            ->label('Modo Sandbox')
                            ->helperText('Ative para testar pagamentos sem cobrança'),
                        Forms\Components\TextInput::make('mp_integrator_id')
                            ->label('Integrator ID')
                            ->maxLength(255)
                            ->helperText('Opcional, usado para identificar integrações'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/default-avatar.png'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->slogan ?? ''),
                
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
                
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Função')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'store' => 'warning',
                        'user' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'store' => 'Loja',
                        'user' => 'Usuário',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at)),
                
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Função')
                    ->options([
                        'admin' => 'Administrador',
                        'store' => 'Loja',
                        'user' => 'Usuário',
                    ]),
                
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
                
                Tables\Filters\Filter::make('email_verified')
                    ->label('E-mail Verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                
                Tables\Filters\Filter::make('email_not_verified')
                    ->label('E-mail Não Verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
                
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
