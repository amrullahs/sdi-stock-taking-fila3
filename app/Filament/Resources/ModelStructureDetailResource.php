<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModelStructureDetailResource\Pages;
use App\Filament\Resources\ModelStructureDetailResource\RelationManagers;
use App\Models\ModelStructureDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModelStructureDetailResource extends Resource
{
    protected static ?string $model = ModelStructureDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Model Structure Details';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('model_structure_id')
                    ->label('Model Structure')
                    ->relationship('modelStructure', 'model')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('qad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('desc1')
                    ->label('Description 1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('desc2')
                    ->label('Description 2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('supplier')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('suplier_code')
                    ->label('Supplier Code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('standard_packing')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->directory('model-structure-details')
                    ->maxSize(2048),
                Forms\Components\TextInput::make('storage')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('wip_id')
                    ->label('WIP ID')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('modelStructure.model')
                    ->label('Model Structure')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('desc1')
                    ->label('Description 1')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('desc2')
                    ->label('Description 2')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suplier_code')
                    ->label('Supplier Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('standard_packing')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square(false)
                    ->getStateUsing(function (ModelStructureDetail $record): ?string {
                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($record->image)) {
                            return $record->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad
                        if (!empty($record->qad)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$record->qad}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$record->qad}.{$ext}");
                                }
                            }
                        }

                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl(url('/images/no-image.svg'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('storage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wip_id')
                    ->label('WIP ID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListModelStructureDetails::route('/'),
            'create' => Pages\CreateModelStructureDetail::route('/create'),
            'edit' => Pages\EditModelStructureDetail::route('/{record}/edit'),
        ];
    }
}
