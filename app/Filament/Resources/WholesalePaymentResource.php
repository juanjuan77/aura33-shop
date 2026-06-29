<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesalePaymentResource\Pages;
use App\Models\WholesalePayment;
use App\Models\WholesaleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WholesalePaymentResource extends Resource
{
    protected static ?string $model = WholesalePayment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';
    protected static ?string $pluralModelLabel = 'Pagos';
    protected static ?string $navigationGroup = 'Mayoristas';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('wholesale_request_id')
                ->label('Mayorista')
                ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('product_name')
                ->label('Producto')
                ->placeholder('Ej: Botella violeta 500ml')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->minValue(1)
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->label('Importe total ($)')
                ->numeric()
                ->prefix('$')
                ->required(),
            Forms\Components\FileUpload::make('receipt')
                ->label('Comprobante')
                ->disk('public')
                ->directory('wholesale-payment-receipts')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wholesaler.business_name')
                    ->label('Mayorista')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cant.'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Importe')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('receipt')
                    ->label('Comprobante')
                    ->disk('public')
                    ->default(null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('wholesale_request_id')
                    ->label('Mayorista')
                    ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWholesalePayments::route('/'),
            'create' => Pages\CreateWholesalePayment::route('/create'),
            'edit'   => Pages\EditWholesalePayment::route('/{record}/edit'),
        ];
    }
}
