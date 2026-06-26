<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignmentResource\Pages;
use App\Filament\Resources\ConsignmentResource\RelationManagers\PaymentsRelationManager;
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
use Illuminate\Support\HtmlString;

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

            Forms\Components\Section::make('')
                ->visibleOn('edit')
                ->schema([
                    Forms\Components\Placeholder::make('resumen_visual')
                        ->label('')
                        ->content(function ($record) {
                            if (! $record) return '';
                            $record->load('items', 'payments');
                            $totalItems    = $record->items->sum('quantity');
                            $totalEntregado = $record->totalDelivered();
                            $totalPagado   = $record->payments->sum('amount');
                            $saldo         = $totalEntregado - $totalPagado;
                            $pct           = $totalEntregado > 0 ? min(100, round($totalPagado / $totalEntregado * 100)) : 0;
                            $saldoColor    = $saldo <= 0 ? '#22c55e' : '#ef4444';
                            $barColor      = $saldo <= 0 ? '#22c55e' : '#f59e0b';

                            return new HtmlString("
                                <div style='display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:8px'>
                                    <div style='background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;text-align:center'>
                                        <div style='font-size:2rem;font-weight:700;color:#16a34a'>$totalItems</div>
                                        <div style='font-size:.75rem;color:#166534;margin-top:4px;text-transform:uppercase;letter-spacing:.05em'>Unidades entregadas</div>
                                    </div>
                                    <div style='background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;text-align:center'>
                                        <div style='font-size:1.5rem;font-weight:700;color:#1d4ed8'>\$" . number_format($totalPagado, 0, ',', '.') . "</div>
                                        <div style='font-size:.75rem;color:#1e40af;margin-top:4px;text-transform:uppercase;letter-spacing:.05em'>Cobrado de \$" . number_format($totalEntregado, 0, ',', '.') . "</div>
                                        <div style='background:#dbeafe;border-radius:99px;height:6px;margin-top:10px'>
                                            <div style='background:{$barColor};height:6px;border-radius:99px;width:{$pct}%'></div>
                                        </div>
                                        <div style='font-size:.7rem;color:#6b7280;margin-top:4px'>{$pct}% cobrado</div>
                                    </div>
                                    <div style='background:#fefce8;border:1px solid #fef08a;border-radius:12px;padding:20px;text-align:center'>
                                        <div style='font-size:1.5rem;font-weight:700;color:{$saldoColor}'>\$" . number_format(abs($saldo), 0, ',', '.') . "</div>
                                        <div style='font-size:.75rem;color:#713f12;margin-top:4px;text-transform:uppercase;letter-spacing:.05em'>" . ($saldo <= 0 ? '✓ Saldado' : 'Saldo pendiente') . "</div>
                                    </div>
                                </div>
                            ");
                        }),
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
                Tables\Columns\TextColumn::make('payments_sum_amount')
                    ->label('Cobrado')
                    ->sum('payments', 'amount')
                    ->money('ARS'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'active' ? 'Activa' : 'Cerrada')
                    ->color(fn($state) => $state === 'active' ? 'success' : 'gray'),
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

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsignments::route('/'),
            'create' => Pages\CreateConsignment::route('/create'),
            'edit'   => Pages\EditConsignment::route('/{record}/edit'),
        ];
    }
}
