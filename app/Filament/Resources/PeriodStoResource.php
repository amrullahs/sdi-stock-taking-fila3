<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodStoResource\Pages;
use App\Filament\Resources\PeriodStoResource\RelationManagers;
use App\Models\PeriodSto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeriodStoResource extends Resource
{
    protected static ?string $model = PeriodSto::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationGroup = 'Stock Taking';
    
    protected static ?string $navigationLabel = 'Period STO';
    
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Period STO Information')
                    ->schema([
                        Forms\Components\DatePicker::make('period_sto')
                            ->label('Period STO')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection(),
                        Forms\Components\TextInput::make('site')
                            ->label('Site')
                            ->required()
                            ->maxLength(255)
                            ->default('7000'),
                        Forms\Components\TextInput::make('created_by')
                            ->label('Created By')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Auto-filled with current user name'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('period_sto')
                    ->label('Period STO')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('site')
                    ->label('Site')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('period_sto')
                    ->form([
                        Forms\Components\DatePicker::make('period_from')
                            ->label('Period From'),
                        Forms\Components\DatePicker::make('period_until')
                            ->label('Period Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['period_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_sto', '>=', $date),
                            )
                            ->when(
                                $data['period_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('period_sto', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('site')
                    ->label('Site')
                    ->options(fn (): array => PeriodSto::distinct()->pluck('site', 'site')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPeriodStos::route('/'),
            'create' => Pages\CreatePeriodSto::route('/create'),
            'view' => Pages\ViewPeriodSto::route('/{record}'),
            'edit' => Pages\EditPeriodSto::route('/{record}/edit'),
        ];
    }
}
