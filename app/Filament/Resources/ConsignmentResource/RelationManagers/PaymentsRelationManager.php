<?php

namespace App\Filament\Resources\ConsignmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Pagos recibidos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->label('Monto pagado ($)')
                ->numeric()
                ->required()
                ->prefix('$'),
            Forms\Components\FileUpload::make('receipt')
                ->label('Comprobante (foto/PDF)')
                ->disk('public')
                ->directory('consignment-receipts')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                ->dehydrated(fn($state) => filled($state))
                ->columnSpanFull(),
            Forms\Components\Textarea::make('notes')
                ->label('Notas')
                ->placeholder('Ej: transferencia Mercado Pago 15/06')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('ARS')
                    ->sortable(),
                Tables\Columns\IconColumn::make('receipt')
                    ->label('Comprobante')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(40),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('+ Agregar pago')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['wholesale_request_id'] = $this->getOwnerRecord()->wholesale_request_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
