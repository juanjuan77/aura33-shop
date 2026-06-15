<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Cupones';
    protected static ?string $modelLabel = 'Cupón';
    protected static ?string $pluralModelLabel = 'Cupones';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Cupón de descuento')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Código')
                        ->required()
                        ->maxLength(20)
                        ->placeholder('VERANO25')
                        ->helperText('Se guarda en mayúsculas automáticamente.')
                        ->dehydrateStateUsing(fn($state) => strtoupper(trim($state)))
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('discount_percent')
                        ->label('% de descuento')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->suffix('%')
                        ->placeholder('15'),

                    Forms\Components\DatePicker::make('valid_from')
                        ->label('Válido desde')
                        ->displayFormat('d/m/Y'),

                    Forms\Components\DatePicker::make('valid_until')
                        ->label('Válido hasta')
                        ->displayFormat('d/m/Y'),

                    Forms\Components\TextInput::make('max_uses')
                        ->label('Usos máximos totales')
                        ->numeric()
                        ->placeholder('Sin límite')
                        ->helperText('Dejá vacío para ilimitado. Independiente del límite por email (1 por cliente).'),

                    Forms\Components\TextInput::make('uses_count')
                        ->label('Veces usado')
                        ->disabled()
                        ->default(0),

                    Forms\Components\Toggle::make('active')
                        ->label('Activo')
                        ->default(true)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Descuento')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('valid_from')
                    ->label('Desde')
                    ->date('d/m/Y')
                    ->placeholder('Sin límite'),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Hasta')
                    ->date('d/m/Y')
                    ->placeholder('Sin límite'),

                Tables\Columns\TextColumn::make('uses_count')
                    ->label('Usos')
                    ->formatStateUsing(fn($state) => $state . ' cliente/s')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
