<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineStoResource\Pages;
use App\Filament\Resources\LineStoResource\RelationManagers;
use App\Models\LineSto;
use App\Models\Line;
use App\Models\PeriodSto;
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
                Forms\Components\Select::make('period_id')
                    ->label('Period STO')
                    ->options(function () {
                        return PeriodSto::where('status', '!=', 'close')
                            ->orderBy('period_sto', 'desc')
                            ->get()
                            ->mapWithKeys(function ($period) {
                                return [$period->id => $period->period_sto->format('d/m/Y') . ' - ' . $period->site];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Period STO'),
                    
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
                    
                Forms\Components\DatePicker::make('tanggal_sto')
                    ->label('Tanggal STO')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now()->format('Y-m-d'))
                    ->disabled()
                    ->dehydrated()
                    ->closeOnDateSelection(),
                    
                Forms\Components\DateTimePicker::make('sto_start_at')
                    ->label('STO Start At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('sto_submit_at')
                    ->label('STO Submit At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('sto_update_at')
                    ->label('STO Update At')
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'onprogress' => 'On Progress',
                        'close' => 'Close',
                    ])
                    ->default('open')
                    ->disabled()
                    ->dehydrated(),
                    
                Forms\Components\TextInput::make('progress')
                    ->label('Progress (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
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
                    
                Tables\Columns\TextColumn::make('tanggal_sto')
                    ->label('Tanggal STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('sto_start_at')
                    ->label('STO Start')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sto_submit_at')
                    ->label('STO Submit')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sto_update_at')
                    ->label('STO Update')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'gray',
                        'onprogress' => 'warning',
                        'close' => 'success',
                    })
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),
                    
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
