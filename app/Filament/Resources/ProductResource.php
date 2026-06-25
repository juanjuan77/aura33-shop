<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información General')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Categoría')
                        ->options(Category::pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('sku')
                        ->label('SKU')
                        ->unique(ignoreRecord: true),

                    Forms\Components\Textarea::make('short_description')
                        ->label('Descripción corta')
                        ->rows(2)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('description')
                        ->label('Descripción completa')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Precios y Stock')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('price_retail')
                        ->label('Precio Minorista ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$'),

                    Forms\Components\TextInput::make('price_wholesale')
                        ->label('Precio Mayorista ($)')
                        ->required()
                        ->numeric()
                        ->prefix('$'),

                    Forms\Components\TextInput::make('stock')
                        ->label('Stock')
                        ->required()
                        ->numeric()
                        ->default(0),
                ]),

            Forms\Components\Section::make('Imágenes')
                ->schema([
                    // Vista previa de la imagen actual (para productos con img/ en public/)
                    Forms\Components\ViewField::make('image_preview')
                        ->label('Imagen actual')
                        ->view('filament.forms.image-preview')
                        ->visibleOn('edit')
                        ->dehydrated(false),

                    Forms\Components\FileUpload::make('image')
                        ->label('Subir nueva imagen principal')
                        ->helperText('Si subís una nueva imagen reemplaza la actual.')
                        ->image()
                        ->disk('public')
                        ->directory('products')
                        ->imagePreviewHeight('200')
                        ->dehydrated(fn ($state) => filled($state)),

                    Forms\Components\FileUpload::make('images')
                        ->label('Galería de imágenes')
                        ->image()
                        ->multiple()
                        ->disk('public')
                        ->directory('products/gallery')
                        ->reorderable(),
                ]),

            Forms\Components\Section::make('Propiedades Energéticas')
                ->schema([
                    Forms\Components\Repeater::make('properties')
                        ->label('Propiedades')
                        ->schema([
                            Forms\Components\TextInput::make('clave')
                                ->label('Propiedad')
                                ->placeholder('chakra, beneficio, color...')
                                ->required(),
                            Forms\Components\TextInput::make('valor')
                                ->label('Valor')
                                ->placeholder('Plexo Solar, Abundancia...')
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->columnSpanFull()
                        ->dehydrateStateUsing(fn($state) => collect($state)
                            ->mapWithKeys(fn($row) => [($row['clave'] ?? '') => ($row['valor'] ?? '')])
                            ->filter(fn($v, $k) => $k !== '')
                            ->all()
                        )
                        ->afterStateHydrated(function ($component, $state) {
                            if (is_array($state)) {
                                $rows = collect($state)
                                    ->filter(fn($v, $k) => is_string($k) && is_string($v))
                                    ->map(fn($v, $k) => ['clave' => $k, 'valor' => $v])
                                    ->values()
                                    ->all();
                                $component->state($rows);
                            }
                        }),
                ]),

            Forms\Components\Section::make('Configuración')
                ->columns(3)
                ->schema([
                    Forms\Components\Toggle::make('featured')
                        ->label('Destacado')
                        ->default(false),

                    Forms\Components\Toggle::make('active')
                        ->label('Activo')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Orden')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->image_url ?: null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_retail')
                    ->label('Min.')
                    ->money('ARS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_wholesale')
                    ->label('May.')
                    ->money('ARS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->sortable(),

                Tables\Columns\IconColumn::make('featured')
                    ->label('Dest.')
                    ->boolean(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('active')->label('Activo'),
                Tables\Filters\TernaryFilter::make('featured')->label('Destacado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('aumentar_precio')
                        ->label('Aumentar precio')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->form([
                            Forms\Components\TextInput::make('monto_minorista')
                                ->label('Sumar al precio minorista ($)')
                                ->numeric()
                                ->default(0)
                                ->required(),
                            Forms\Components\TextInput::make('monto_mayorista')
                                ->label('Sumar al precio mayorista ($)')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function (Product $record) use ($data) {
                                $record->update([
                                    'price_retail'    => $record->price_retail + (float) $data['monto_minorista'],
                                    'price_wholesale' => $record->price_wholesale + (float) $data['monto_mayorista'],
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
