<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineModelDetailResource\Pages;
use App\Filament\Resources\LineModelDetailResource\RelationManagers;
use App\Models\LineModelDetail;
use App\Models\Line;
use App\Models\ModelStructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LineModelDetailResource extends Resource
{
    protected static ?string $model = LineModelDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Line Model Detail';

    protected static ?string $pluralModelLabel = 'Line Model Details';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('line_id')
                    ->label('Line')
                    ->relationship('line', 'line')
                    ->required()
                    ->searchable(),
                    
                Forms\Components\Select::make('model_id')
                    ->label('Model')
                    ->relationship('model', 'model')
                    ->required()
                    ->searchable(),
                    
                Forms\Components\TextInput::make('type')
                    ->label('Type')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('qad_number')
                    ->label('QAD Number')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('part_name')
                    ->label('Part Name')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('part_number')
                    ->label('Part Number')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Textarea::make('desc')
                    ->label('Description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                    
                Forms\Components\TextInput::make('supplier')
                    ->label('Supplier')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('suplier_code')
                    ->label('Supplier Code')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('std_packing')
                    ->label('Standard Packing')
                    ->numeric()
                    ->minValue(0),
                    
                Forms\Components\TextInput::make('storage')
                    ->label('Storage')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('wip_id')
                    ->label('WIP ID')
                    ->maxLength(255),
                    
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->directory('line-model-details')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('model.model')
                    ->label('Model')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('qad_number')
                    ->label('QAD Number')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('part_name')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('part_number')
                    ->label('Part Number')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('desc')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('suplier_code')
                    ->label('Supplier Code')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('std_packing')
                    ->label('Std Packing')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('storage')
                    ->label('Storage')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('wip_id')
                    ->label('WIP ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square(false)
                    ->circular(false)
                    ->width(60)
                    ->height(60)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
                    ->getStateUsing(function (\App\Models\LineModelDetail $record): ?string {
                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($record->image)) {
                            return $record->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad_number
                        if (!empty($record->qad_number)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$record->qad_number}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$record->qad_number}.{$ext}");
                                }
                            }
                        }

                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl(url('/images/no-image.svg'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('line_id')
                    ->label('Filter by Line')
                    ->relationship('line', 'line')
                    ->searchable()
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('model_id')
                    ->label('Filter by Model')
                    ->relationship('model', 'model')
                    ->searchable()
                    ->multiple(),
                    
                Tables\Filters\Filter::make('qad_number')
                    ->form([
                        Forms\Components\TextInput::make('qad_number')
                            ->label('QAD Number')
                            ->placeholder('Search QAD Number...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['qad_number'],
                                fn (Builder $query, $qad): Builder => $query->where('qad_number', 'like', "%{$qad}%")
                            );
                    }),
                    
                Tables\Filters\Filter::make('part_name')
                    ->form([
                        Forms\Components\TextInput::make('part_name')
                            ->label('Part Name')
                            ->placeholder('Search Part Name...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['part_name'],
                                fn (Builder $query, $part): Builder => $query->where('part_name', 'like', "%{$part}%")
                            );
                    }),
                    
                Tables\Filters\Filter::make('supplier')
                    ->form([
                        Forms\Components\TextInput::make('supplier')
                            ->label('Supplier')
                            ->placeholder('Search Supplier...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['supplier'],
                                fn (Builder $query, $supplier): Builder => $query->where('supplier', 'like', "%{$supplier}%")
                            );
                    }),
                    
                Tables\Filters\Filter::make('std_packing_range')
                    ->form([
                        Forms\Components\TextInput::make('std_packing_from')
                            ->label('Std Packing From')
                            ->numeric(),
                        Forms\Components\TextInput::make('std_packing_to')
                            ->label('Std Packing To')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['std_packing_from'],
                                fn (Builder $query, $from): Builder => $query->where('std_packing', '>=', $from)
                            )
                            ->when(
                                $data['std_packing_to'],
                                fn (Builder $query, $to): Builder => $query->where('std_packing', '<=', $to)
                            );
                    }),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLineModelDetails::route('/'),
            'create' => Pages\CreateLineModelDetail::route('/create'),
            'edit' => Pages\EditLineModelDetail::route('/{record}/edit'),
        ];
    }
}
