<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineStoDetailResource\Pages;
use App\Filament\Resources\LineStoDetailResource\RelationManagers;
use App\Models\LineStoDetail;
use App\Models\PeriodSto;
use App\Models\LineSto;
use App\Models\LineModelDetail;
use App\Models\Line;
use App\Exports\LineStoDetailExport;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LineStoDetailResource extends Resource
{
    protected static ?string $model = LineStoDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Line STO';

    protected static ?string $navigationLabel = 'Line STO Detail';

    protected static ?string $modelLabel = 'Line STO Detail';

    protected static ?string $pluralModelLabel = 'Line STO Details';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('period_id')
                    ->relationship('periodSto', 'period_sto')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('line_id')
                    ->relationship('line', 'line')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('line_model_detail_id')
                    ->relationship('lineModelDetail', 'id')
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
                Tables\Columns\TextColumn::make('periodSto.period_sto')
                    ->label('Period STO')
                    ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d-m-Y') : '-')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('line.line')
                    ->label('Line')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lineModelDetail.model_id')
                    ->label('Model')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lineModelDetail.qad_number')
                    ->label('QAD')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('lineModelDetail.image')
                    ->label('Image')
                    ->square(false)
                    ->circular(false)
                    ->width(60)
                    ->height(60)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain; width: 100%; height: 100%;'
                    ])
                    ->getStateUsing(function (\App\Models\LineStoDetail $record): ?string {
                        // Akses lineModelDetail melalui relasi
                        $lineModelDetail = $record->lineModelDetail;

                        if (!$lineModelDetail) {
                            return null;
                        }

                        // Jika field image tidak null, gunakan nilai aslinya
                        if (!empty($lineModelDetail->image)) {
                            return $lineModelDetail->image;
                        }

                        // Jika image null, cari file berdasarkan nilai qad_number
                        if (!empty($lineModelDetail->qad_number)) {
                            $extensions = ['png', 'jpg', 'jpeg', 'svg'];
                            foreach ($extensions as $ext) {
                                $imagePath = storage_path("app/public/img/{$lineModelDetail->qad_number}.{$ext}");
                                if (file_exists($imagePath)) {
                                    return asset("storage/img/{$lineModelDetail->qad_number}.{$ext}");
                                }
                            }
                        }

                        // Jika tidak ada file yang ditemukan, return null untuk fallback ke defaultImageUrl
                        return null;
                    })
                    ->defaultImageUrl(url('/images/no-image.svg'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lineModelDetail.part_name')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('lineModelDetail.part_number')
                    ->label('Part Number')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('total_on_hand')
                    ->label('Total On Hand')
                    ->numeric()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->total_on_hand;
                    }),
                Tables\Columns\TextInputColumn::make('storage_count')
                    ->label('Storage Count')
                    ->type('number')
                    ->rules(['max:6'])
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('25px')
                    ->extraAttributes(['class' => 'text-center']),
                Tables\Columns\TextInputColumn::make('wip_count')
                    ->label('WIP Count')
                    ->type('number')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('25px'),
                Tables\Columns\TextInputColumn::make('ng_count')
                    ->label('NG Count')
                    ->type('number')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable()
                    ->width('25px'),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('Total Count')
                    ->formatStateUsing(fn($record) => (
                        ($record->storage_count ?? 0) +
                        ($record->wip_count ?? 0) +
                        ($record->ng_count ?? 0)
                    ))
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
                SelectFilter::make('period_id')
                    ->label('Period STO')
                    ->relationship('periodSto', 'period_sto')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('line_id')
                    ->label('Line')
                    ->options(Line::distinct()->pluck('line', 'id')->filter(fn($value) => !empty($value) && !is_null($value))->sort())
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('lineSto', function (Builder $query) use ($value) {
                                $query->where('line_id', $value);
                            })
                        );
                    }),
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
            'index' => Pages\ListLineStoDetails::route('/'),
            'create' => Pages\CreateLineStoDetail::route('/create'),
            'edit' => Pages\EditLineStoDetail::route('/{record}/edit'),
        ];
    }
}
