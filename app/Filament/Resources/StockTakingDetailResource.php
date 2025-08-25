<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTakingDetailResource\Pages;
use App\Filament\Resources\StockTakingDetailResource\RelationManagers;
use App\Models\StockTakingDetail;
use App\Models\StockTaking;
use App\Models\ModelStructureDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockTakingDetailResource extends Resource
{
    protected static ?string $model = StockTakingDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Stock Taking';

    protected static ?string $navigationLabel = 'Stock Taking Detail';

    protected static ?string $modelLabel = 'Stock Taking Detail';

    protected static ?string $pluralModelLabel = 'Stock Taking Details';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('stock_taking_id')
                    ->relationship('stockTaking', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->modelStructure->model . ' - ' . ($record->periodSto ? \Carbon\Carbon::parse($record->periodSto->period_sto)->format('d-m-Y') : 'N/A'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('model_structure_detail_id')
                    ->relationship('modelStructureDetail', 'part_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('storage_count')
                    ->label('Storage Count')
                    ->numeric()
                    ->placeholder('Enter storage count')
                    ->minValue(0),
                Forms\Components\TextInput::make('wip_count')
                    ->label('WIP Count')
                    ->numeric()
                    ->placeholder('Enter WIP count')
                    ->minValue(0),
                Forms\Components\TextInput::make('ng_count')
                    ->label('NG Count')
                    ->numeric()
                    ->placeholder('Enter NG count')
                    ->minValue(0),
                Forms\Components\TextInput::make('total_count')
                    ->label('Total Count')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stockTaking.periodSto.period_sto')
                    ->label('Period STO')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d-m-Y') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stockTaking.model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('modelStructureDetail.image_url')
                    ->label('Image')
                    ->circular(false)
                    ->square(false)
                    ->width(200)
                    ->height(150)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
                    ->getStateUsing(function ($record): ?string {
                        $modelStructureDetail = $record->modelStructureDetail;
                        if (!$modelStructureDetail) {
                            return null;
                        }
                        
                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($modelStructureDetail->image)) {
                            return $modelStructureDetail->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad
                        if (!empty($modelStructureDetail->qad)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$modelStructureDetail->qad}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$modelStructureDetail->qad}.{$ext}");
                                }
                            }
                        }

                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl('/images/no-image.svg'),
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('modelStructureDetail.part_number')
                        ->searchable()
                        ->sortable()
                        ->weight('bold')
                        ->color('primary'),
                    Tables\Columns\TextColumn::make('modelStructureDetail.part_name')
                        ->searchable()
                        ->sortable()
                        ->color('gray')
                        ->size('sm')
                        ->wrap(),
                ]),
                Tables\Columns\TextColumn::make('total_on_hand')
                    ->label('Total On Hand')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->total_on_hand;
                    }),
                Tables\Columns\TextColumn::make('modelStructureDetail.storage')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('storage_count')
                    ->label('Storage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wip_count')
                    ->label('WIP')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ng_count')
                    ->label('NG')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return ($record->storage_count ?? 0) + ($record->wip_count ?? 0) + ($record->ng_count ?? 0);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
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
            'index' => Pages\ListStockTakingDetails::route('/'),
            'edit' => Pages\EditStockTakingDetail::route('/{record}/edit'),
        ];
    }
}
