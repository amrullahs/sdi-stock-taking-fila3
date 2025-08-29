<?php

namespace App\Filament\Resources;

use Rmsramos\Activitylog\Resources\ActivitylogResource as BaseActivitylogResource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;

class ActivitylogResource extends BaseActivitylogResource
{
    protected static ?string $navigationGroup = 'Log Activity';
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?int $navigationSort = 1;
    
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->can('view_activity_log') ?? false;
    }
    
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user?->can('view_activity_log') ?? false;
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'gray',
                        'created' => 'info',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Subject Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log Type')
                    ->options([
                        'default' => 'Default',
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('subject_type')
                    ->label('Model Type')
                    ->options([
                        'App\\Models\\PeriodSto' => 'Period STO',
                        'App\\Models\\LineSto' => 'Line STO',
                        'App\\Models\\LineStoDetail' => 'Line STO Detail',
                        'App\\Models\\User' => 'User',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From Date'),
                        DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }
}