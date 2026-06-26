<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignmentResource\Pages;
use App\Models\Consignment;
use App\Models\ConsignmentPayment;
use App\Models\ConsignmentReport;
use App\Models\WholesaleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class ConsignmentResource extends Resource
{
    protected static ?string $model = Consignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Consignaciones';
    protected static ?string $modelLabel = 'Consignación';
    protected static ?string $pluralModelLabel = 'Consignaciones';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Local / Mayorista')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('wholesale_request_id')
                        ->label('Mayorista')
                        ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(['active' => 'Activa', 'closed' => 'Cerrada'])
                        ->default('active')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Productos entregados')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->label('')
                        ->schema([
                            Forms\Components\TextInput::make('product_name')
                                ->label('Producto')
                                ->required()
                                ->placeholder('Botella Amatista'),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->default(1),
                            Forms\Components\TextInput::make('unit_price')
                                ->label('Precio unit. ($)')
                                ->numeric()
                                ->required()
                                ->prefix('$'),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->addActionLabel('+ Agregar producto'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wholesaler.business_name')
                    ->label('Local')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wholesaler.city')
                    ->label('Ciudad'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Productos')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($s) => $s === 'active' ? 'Activa' : 'Cerrada')
                    ->color(fn($s) => $s === 'active' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['active' => 'Activa', 'closed' => 'Cerrada']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsignments::route('/'),
            'create' => Pages\CreateConsignment::route('/create'),
            'edit'   => Pages\EditConsignment::route('/{record}/edit'),
        ];
    }
}
