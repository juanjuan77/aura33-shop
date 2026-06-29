<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesaleDeliveryResource\Pages;
use App\Models\WholesaleDelivery;
use App\Models\WholesaleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WholesaleDeliveryResource extends Resource
{
    protected static ?string $model = WholesaleDelivery::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Entregas';
    protected static ?string $modelLabel = 'Entrega';
    protected static ?string $pluralModelLabel = 'Entregas';
    protected static ?string $navigationGroup = 'Mayoristas';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('wholesale_request_id')
                ->label('Mayorista')
                ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('quantity')
                ->label('Cantidad de botellas')
                ->numeric()
                ->minValue(1)
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->label('Detalle (opcional)')
                ->placeholder('Qué botellas, colores, modelos...')
                ->rows(3),
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
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Botellas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Detalle')
                    ->limit(50)
                    ->wrap(),
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
            'index'  => Pages\ListWholesaleDeliveries::route('/'),
            'create' => Pages\CreateWholesaleDelivery::route('/create'),
            'edit'   => Pages\EditWholesaleDelivery::route('/{record}/edit'),
        ];
    }
}
