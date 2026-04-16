@php
    use App\Filament\App\Resources\Patients\Resources\PatientHistories\PatientHistoryResource;
    use App\Models\Patient;
    use App\Models\PatientHistory;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Carbon;

    /** @var Collection<int, PatientHistory> $histories */
    /** @var Patient $ownerRecord */
    /** @var string|null $diseaseSearch */
@endphp

<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:max-w-sm">
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="diseaseSearch"
                    placeholder="Search histories by disease"
                />
            </x-filament::input.wrapper>
        </div>

        <div class="flex items-center justify-between gap-3 sm:justify-end">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $histories->count() }} visit{{ $histories->count() === 1 ? '' : 's' }}
            </p>

            <x-filament::button
                tag="a"
                :href="PatientHistoryResource::getUrl('create', ['patient' => $ownerRecord->Id])"
                icon="heroicon-o-plus"
            >
                Add History
            </x-filament::button>
        </div>
    </div>

    @forelse ($histories as $history)
        <x-filament::section
            compact
            :heading="'Visit ' . ($history->CreatedDate ? Carbon::parse($history->CreatedDate)->timezone(config('app.timezone'))->format('d/m/Y h:i A') : '-')"
        >
            <x-slot name="afterHeader">
                <div class="flex flex-wrap items-center gap-2">
                    <x-filament::button
                        size="sm"
                        color="gray"
                        tag="a"
                        :href="PatientHistoryResource::getUrl('edit', ['record' => $history->Id, 'patient' => $ownerRecord->Id])"
                    >
                        Edit
                    </x-filament::button>

                    <x-filament::button
                        size="sm"
                        color="gray"
                        wire:click="repeatHistory('{{ $history->Id }}')"
                    >
                        Repeat
                    </x-filament::button>

                    <x-filament::button
                        size="sm"
                        color="gray"
                        tag="a"
                        :href="route('order.print', ['history' => $history->Id])"
                        target="_blank"
                    >
                        Print
                    </x-filament::button>

                    <x-filament::button
                        size="sm"
                        color="gray"
                        tag="a"
                        :href="route('order.print-meds', ['history' => $history->Id])"
                        target="_blank"
                    >
                        Print Meds
                    </x-filament::button>
                </div>
            </x-slot>

            @include('filament.app.tables.patient-history-details', ['record' => $history])
        </x-filament::section>
    @empty
        <x-filament::section compact heading="No Histories">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if (filled($diseaseSearch))
                    No history entries found for "{{ $diseaseSearch }}".
                @else
                    This patient has no history entries yet.
                @endif
            </p>
        </x-filament::section>
    @endforelse
</div>
