<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Productos del Pedido')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship('items')
                        ->schema([
                            Forms\Components\TextInput::make('product_name')
                                ->label('Producto')
                                ->disabled()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('quantity')
                                ->label('Cant.')
                                ->disabled(),
                            Forms\Components\TextInput::make('unit_price')
                                ->label('Precio unit.')
                                ->disabled()
                                ->prefix('$'),
                            Forms\Components\TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->disabled()
                                ->prefix('$'),
                        ])
                        ->columns(5)
                        ->disabled()
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->label(''),
                ]),

            Forms\Components\Section::make('Datos del Cliente')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('order_number')->label('Nro. Pedido')->disabled(),
                    Forms\Components\Select::make('customer_type')
                        ->label('Tipo de cliente')
                        ->options(['retail' => 'Minorista', 'wholesale' => 'Mayorista'])
                        ->required(),
                    Forms\Components\TextInput::make('customer_name')->label('Nombre')->required(),
                    Forms\Components\TextInput::make('customer_email')->label('Email')->email()->required(),
                    Forms\Components\TextInput::make('customer_phone')->label('Teléfono')->tel(),
                    Forms\Components\Placeholder::make('whatsapp_link')
                        ->label('WhatsApp')
                        ->content(fn ($record) => $record?->customer_phone
                            ? new \Illuminate\Support\HtmlString(
                                '<a href="https://wa.me/' . preg_replace('/[^0-9]/', '', str_starts_with(preg_replace('/[^0-9]/', '', $record->customer_phone), '54') ? $record->customer_phone : '54' . ltrim(preg_replace('/[^0-9]/', '', $record->customer_phone), '0')) . '" target="_blank" style="display:inline-flex;align-items:center;gap:7px;background:#25d366;color:#fff;padding:7px 14px;border-radius:6px;font-size:0.82rem;font-weight:600;text-decoration:none;">
                                    <svg xmlns=\'http://www.w3.org/2000/svg\' width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'currentColor\'><path d=\'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z\'/></svg>
                                    Abrir WhatsApp
                                </a>'
                            )
                            : new \Illuminate\Support\HtmlString('<span style="color:#aaa;font-size:0.82rem;">Sin teléfono</span>')
                        ),
                ]),

            Forms\Components\Section::make('Envío')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('shipping_address')->label('Dirección')->columnSpanFull(),
                    Forms\Components\TextInput::make('shipping_city')->label('Ciudad'),
                    Forms\Components\TextInput::make('shipping_province')->label('Provincia'),
                    Forms\Components\TextInput::make('shipping_postal_code')->label('Código Postal'),
                    Forms\Components\Textarea::make('shipping_notes')->label('Notas de envío')->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Pago y Estado')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(Order::STATUSES)
                        ->required(),
                    Forms\Components\TextInput::make('payment_method')->label('Medio de pago')->disabled(),
                    Forms\Components\TextInput::make('transfer_receipt')
                        ->label('Comprobante')
                        ->disabled()
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('ver')
                                ->icon('heroicon-o-eye')
                                ->url(fn ($record) => $record?->transfer_receipt ? asset('storage/' . $record->transfer_receipt) : null)
                                ->openUrlInNewTab()
                                ->visible(fn ($record) => (bool) $record?->transfer_receipt)
                        ),
                    Forms\Components\DateTimePicker::make('confirmed_at')->label('Confirmado el'),
                ]),

            Forms\Components\Section::make('Totales')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('subtotal')->label('Subtotal ($)')->disabled()->prefix('$'),
                    Forms\Components\TextInput::make('shipping_cost')->label('Envío ($)')->disabled()->prefix('$'),
                    Forms\Components\TextInput::make('discount_amount')->label('Descuento ($)')->disabled()->prefix('-$')
                        ->visible(fn ($record) => ($record?->discount_amount ?? 0) > 0),
                    Forms\Components\TextInput::make('coupon_code')->label('Cupón')->disabled()
                        ->visible(fn ($record) => (bool) $record?->coupon_code),
                    Forms\Components\TextInput::make('mp_surcharge')->label('Recargo MP ($)')->disabled()->prefix('$')
                        ->visible(fn ($record) => ($record?->mp_surcharge ?? 0) > 0),
                    Forms\Components\TextInput::make('total')->label('TOTAL ($)')->disabled()->prefix('$'),
                ]),

            Forms\Components\Section::make('Notas internas')
                ->schema([
                    Forms\Components\Textarea::make('notes')->label('Notas')->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $statusColors = [
            'pending'          => 'gray',
            'transfer_pending' => 'warning',
            'receipt_received' => 'info',
            'mp_pending'       => 'warning',
            'confirmed'        => 'success',
            'shipped'          => 'info',
            'delivered'        => 'success',
            'cancelled'        => 'danger',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'wholesale' ? 'Mayorista' : 'Minorista')
                    ->color(fn ($state) => $state === 'wholesale' ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('ARS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => $statusColors[$state] ?? 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Order::STATUSES),
                Tables\Filters\SelectFilter::make('customer_type')
                    ->label('Tipo')
                    ->options(['retail' => 'Minorista', 'wholesale' => 'Mayorista']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->where('status', '!=', 'cancelled'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
