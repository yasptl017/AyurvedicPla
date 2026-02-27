<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Tables;

use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
use App\Models\Disease;
use App\Models\Medicine;
use App\Models\PatientHistory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PatientHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'patient',
                'diseases',
                'symptoms',
                'prescriptions.medicine',
            ]))
            ->defaultSort('CreatedDate', 'desc')
            ->columns([
                TextColumn::make('CreatedDate')
                    ->label('Visit')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('diseases.Name')
                    ->label('')
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas(
                        'diseases',
                        fn (Builder $q) => $q->where('Diseases.Name', 'like', "%{$search}%")
                    ))
                    ->toggleable(isToggledHiddenByDefault: true),

                ViewColumn::make('details')
                    ->label('Details')
                    ->view('filament.app.tables.patient-history-details'),
            ])
            ->filters([
                SelectFilter::make('disease')
                    ->label('Disease')
                    ->options(fn () => Disease::query()->orderBy('Name')->pluck('Name', 'Id'))
                    ->searchable()
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $q, $id) => $q->whereHas('diseases', fn (Builder $d) => $d->where('Diseases.Id', $id))
                    )),

                SelectFilter::make('medicine')
                    ->label('Medicine')
                    ->options(fn () => Medicine::query()->orderBy('Name')->pluck('Name', 'Id'))
                    ->searchable()
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $q, $id) => $q->whereHas('prescriptions', fn (Builder $p) => $p->where('MedicineId', $id))
                    )),

                Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        DatePicker::make('from')->label('From')->native(false),
                        DatePicker::make('until')->label('Until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('CreatedDate', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('CreatedDate', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From: '.\Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until: '.\Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),

                TrashedFilter::make(),
            ])
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                EditAction::make(),
                Action::make('replicate')
                    ->label('Repeat')
                    ->icon('heroicon-o-document-duplicate') // Filament v3 syntax
                    ->requiresConfirmation()
                    ->action(function (PatientHistory $record) {
                        $newHistory = DB::transaction(function () use ($record) {
                            $newHistory = $record->replicate(['prescriptions_count']);
                            $newHistory->CreatedDate = now();
                            $newHistory->save();

                            foreach ($record->prescriptions as $prescription) {
                                $newHistory->prescriptions()->save($prescription->replicate());
                            }

                            $diseases = $record->diseases()->withPivot(['DiseaseTypeId'])->get()
                                ->mapWithKeys(fn ($item) => [
                                    $item->Id => ['DiseaseTypeId' => $item->pivot->DiseaseTypeId],
                                ]);
                            $newHistory->diseases()->attach($diseases);

                            $newHistory->symptoms()->attach($record->symptoms()->pluck('SymptomId'));

                            $panchakarmas = $record->panchakarmas()->withPivot(['Detail'])->get()
                                ->mapWithKeys(fn ($item) => [
                                    $item->Id => ['Detail' => $item->pivot->Detail],
                                ]);
                            $newHistory->panchakarmas()->attach($panchakarmas);

                            $relations = ['womenHistory', 'vital', 'rogaPariksa', 'hetuPariksa'];

                            foreach ($relations as $relation) {
                                if ($record->$relation) {
                                    $newHistory->$relation()->save($record->$relation->replicate());
                                }
                            }

                            foreach ($record->patientFiles as $file) {
                                $newHistory->patientFiles()->create($file->only(['File']));
                            }

                            foreach ($record->sketches as $sketch) {
                                $newHistory->sketches()->create($sketch->only(['sketch']));
                            }

                            foreach ($record->captures as $capture) {
                                $newHistory->captures()->create($capture->only(['capture']));
                            }

                            return $newHistory;
                        });

                        return redirect(PatientHistoryResource::getUrl('edit', ['record' => $newHistory, 'patient' => $newHistory->patient]));
                    }),

                Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon(Heroicon::Printer)
                    ->url(fn (PatientHistory $record): string => route('order.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
