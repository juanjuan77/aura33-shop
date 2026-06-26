<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsignmentReportResource\Pages;
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
use Filament\Notifications\Notification;

class ConsignmentReportResource extends Resource
{
    protected static ?string $model = ConsignmentReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Reportes / Pagos';
    protected static ?string $modelLabel = 'Reporte';
    protected static ?string $pluralModelLabel = 'Reportes y Pagos';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Reporte de ventas')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('wholesale_request_id')
                        ->label('Mayorista / Local')
                        ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('consignment_id')
                        ->label('Consignación')
                        ->options(fn($get) => Consignment::where('wholesale_request_id', $get('wholesale_request_id'))
                            ->where('status', 'active')
                            ->with('items')
                            ->get()
                            ->mapWithKeys(fn($c) => [$c->id => 'Entrega #'.$c->id.' — '.$c->created_at->format('d/m/Y')])
                        )
                        ->nullable(),
                    Forms\Components\Textarea::make('description')
                        ->label('Descripción (qué vendió)')
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Ej: 3 botellas amatista, 2 ojo de tigre'),
                    Forms\Components\TextInput::make('amount')
                        ->label('Monto vendido ($)')
                        ->numeric()
                        ->required()
                        ->prefix('$'),
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(['pending' => 'Pendiente', 'confirmed' => 'Confirmado', 'rejected' => 'Rechazado'])
                        ->default('pending')
                        ->required(),
                    Forms\Components\FileUpload::make('receipt')
                        ->label('Comprobante')
                        ->disk('public')
                        ->directory('consignment-receipts')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp','application/pdf'])
                        ->columnSpanFull()
                        ->dehydrated(fn($state) => filled($state)),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Notas internas')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wholesaler.business_name')
                    ->label('Local')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(40),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending'   => 'Pendiente',
                        'confirmed' => 'Confirmado',
                        'rejected'  => 'Rechazado',
                        default     => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'confirmed' => 'success',
                        'rejected'  => 'danger',
                        default     => 'warning',
                    }),
                Tables\Columns\IconColumn::make('receipt')
                    ->label('Comprobante')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(['pending' => 'Pendiente', 'confirmed' => 'Confirmado', 'rejected' => 'Rechazado']),
                Tables\Filters\SelectFilter::make('wholesale_request_id')
                    ->label('Local')
                    ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'confirmed', 'confirmed_at' => now()]);
                        Notification::make()->title('Reporte confirmado')->success()->send();
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('+ Nuevo reporte'),
                \Filament\Tables\Actions\Action::make('nuevo_pago')
                    ->label('+ Registrar pago')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('wholesale_request_id')
                            ->label('Mayorista / Local')
                            ->options(WholesaleRequest::where('status', 'approved')->pluck('business_name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto pagado ($)')
                            ->numeric()
                            ->required()
                            ->prefix('$'),
                        Forms\Components\FileUpload::make('receipt')
                            ->label('Comprobante')
                            ->disk('public')
                            ->directory('consignment-receipts')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp','application/pdf']),
                        Forms\Components\Textarea::make('notes')->label('Notas'),
                    ])
                    ->action(function (array $data) {
                        ConsignmentPayment::create($data);
                        Notification::make()->title('Pago registrado')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConsignmentReports::route('/'),
            'create' => Pages\CreateConsignmentReport::route('/create'),
            'edit'   => Pages\EditConsignmentReport::route('/{record}/edit'),
        ];
    }
}
