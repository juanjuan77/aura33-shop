<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WholesaleRequestResource\Pages;
use App\Models\WholesaleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class WholesaleRequestResource extends Resource
{
    protected static ?string $model = WholesaleRequest::class;
    protected static ?string $navigationIcon   = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel  = 'Solicitudes Mayoristas';
    protected static ?string $modelLabel       = 'Solicitud Mayorista';
    protected static ?string $pluralModelLabel = 'Solicitudes Mayoristas';
    protected static ?int    $navigationSort   = 5;

    public static function getNavigationBadge(): ?string
    {
        $count = WholesaleRequest::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del Solicitante')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nombre')->disabled(),
                    Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                    Forms\Components\TextInput::make('phone')->label('Teléfono')->disabled(),
                    Forms\Components\TextInput::make('business_name')->label('Negocio')->disabled(),
                    Forms\Components\TextInput::make('business_type')
                        ->label('Tipo')
                        ->formatStateUsing(fn ($state) => WholesaleRequest::BUSINESS_TYPES[$state] ?? $state)
                        ->disabled(),
                    Forms\Components\TextInput::make('cuit')->label('CUIT/DNI')->disabled(),
                    Forms\Components\TextInput::make('city')->label('Ciudad')->disabled(),
                    Forms\Components\TextInput::make('province')->label('Provincia')->disabled(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Mensaje del solicitante')
                        ->disabled()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Decisión')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options(WholesaleRequest::STATUSES)
                        ->required(),
                    Forms\Components\DateTimePicker::make('reviewed_at')
                        ->label('Fecha de revisión'),
                    Forms\Components\Toggle::make('is_consignment')
                        ->label('¿Trabaja en consignación?')
                        ->helperText('Activa el módulo de consignación en su portal y en el admin.')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Notas internas')
                        ->placeholder('Solo visible en el admin...')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('business_name')
                    ->label('Negocio')
                    ->searchable(),

                Tables\Columns\TextColumn::make('business_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => WholesaleRequest::BUSINESS_TYPES[$state] ?? $state)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => WholesaleRequest::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),

                Tables\Columns\IconColumn::make('is_consignment')
                    ->label('Consig.')
                    ->boolean()
                    ->trueIcon('heroicon-o-cube')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(WholesaleRequest::STATUSES),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->modalHeading('Aprobar solicitud')
                    ->modalDescription(fn ($record) => "Se aprobará a {$record->name} ({$record->email}) como mayorista.")
                    ->form([
                        Forms\Components\Toggle::make('is_consignment')
                            ->label('¿Trabaja en consignación?')
                            ->helperText('Activa el módulo de consignación en su portal.')
                            ->default(false),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status'         => 'approved',
                        'is_consignment' => $data['is_consignment'] ?? false,
                        'reviewed_at'    => Carbon::now(),
                    ])),

                Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('¿Rechazar solicitud?')
                    ->action(fn ($record) => $record->update(['status' => 'rejected', 'reviewed_at' => Carbon::now()])),

                Tables\Actions\EditAction::make()->label('Ver detalle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWholesaleRequests::route('/'),
            'create' => Pages\CreateWholesaleRequest::route('/create'),
            'edit'   => Pages\EditWholesaleRequest::route('/{record}/edit'),
        ];
    }
}
