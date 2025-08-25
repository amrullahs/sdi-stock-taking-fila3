<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineStoResource\Pages;
use App\Filament\Resources\LineStoResource\RelationManagers;
use App\Models\LineSto;
use App\Models\Line;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LineStoResource extends Resource
{
    protected static ?string $model = LineSto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Line STO';
    
    protected static ?int $navigationSort = -10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('period_sto')
                    ->label('Period STO')
                    ->required(),
                    
                Forms\Components\Select::make('line_id')
                    ->label('Line')
                    ->options(Line::all()->pluck('line', 'id'))
                    ->searchable()
                    ->required(),
                    
                Forms\Components\TextInput::make('created_by')
                    ->label('Created By')
                    ->maxLength(255)
                    ->default(fn() => Auth::user()?->name ?? Auth::user()?->email)
                    ->disabled()
                    ->dehydrated(),
                    
                Forms\Components\TextInput::make('site')
                    ->label('Site')
                    ->maxLength(255)
                    ->default('7000')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('period_sto')
                    ->label('Period STO')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Created By')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('site')
                    ->label('Site')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListLineStos::route('/'),
            'create' => Pages\CreateLineSto::route('/create'),
            'edit' => Pages\EditLineSto::route('/{record}/edit'),
        ];
    }
}
