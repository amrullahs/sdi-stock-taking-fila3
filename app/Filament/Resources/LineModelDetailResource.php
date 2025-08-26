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
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('model.model')
                    ->label('Model')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('qad_number')
                    ->label('QAD Number')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('part_name')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('part_number')
                    ->label('Part Number')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('std_packing')
                    ->label('Std Packing')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('storage')
                    ->label('Storage')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->square()
                    ->size(40),
                    
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
                //
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
