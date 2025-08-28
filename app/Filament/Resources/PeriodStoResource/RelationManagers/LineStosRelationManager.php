<?php

namespace App\Filament\Resources\PeriodStoResource\RelationManagers;

use App\Models\Line;
use App\Models\LineSto;
use App\Exports\LineStoDetailExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LineStosRelationManager extends RelationManager
{
    protected static string $relationship = 'lineStos';

    protected static ?string $recordTitleAttribute = 'line.line';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('line_id')
                    ->label('Line')
                    ->options(Line::all()->pluck('line', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled(fn($record) => $record && $record->status === 'onprogress'),

                // created_by akan diisi otomatis oleh LineStoObserver

                Forms\Components\DateTimePicker::make('sto_start_at')
                    ->label('Start STO At')
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'onprogress' => 'On Progress',
                        'close' => 'Close',
                    ])
                    ->default('open')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('line.line')
            ->columns([
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_by')
                    ->label('Created By')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 75 => 'info',
                        $state >= 50 => 'warning',
                        $state >= 25 => 'orange',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('sto_start_at')
                    ->label('Start STO At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Not started'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'danger',
                        'onprogress' => 'warning',
                        'close' => 'success',
                        default => 'secondary',
                    })
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'onprogress' => 'On Progress',
                        'close' => 'Close',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        // Get period ID from the owner record
                        $periodId = $this->ownerRecord->id;
                        
                        $filename = 'line_sto_detail_export_period_' . $periodId . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                        
                        return Excel::download(new LineStoDetailExport($periodId, null), $filename);
                    })
                    ->tooltip('Export Line STO Detail data to Excel file')
                    ->color('success'),
                // Tables\Actions\CreateAction::make()
                //     ->mutateFormDataUsing(function (array $data): array {
                //         // Validasi kombinasi period_id dan line_id yang unik
                //         $existingLineSto = LineSto::where('period_id', $this->ownerRecord->id)
                //             ->where('line_id', $data['line_id'])
                //             ->first();

                //         if ($existingLineSto) {
                //             $line = Line::find($data['line_id']);
                //             $lineName = $line ? $line->line : 'Unknown Line';

                //             throw new \Exception("Sudah ada Line STO untuk {$lineName} pada periode ini. Silahkan pilih Line lain.");
                //         }

                //         $data['period_id'] = $this->ownerRecord->id;
                //         return $data;
                //     }),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
