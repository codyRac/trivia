<x-filament-panels::page>
    {{-- Refresh so newly revealed clues show up without reloading --}}
    <div wire:poll.5s class="space-y-6">
        <x-filament::section>
            <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center;">
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.7;">Status</div>
                    <x-filament::badge
                        size="lg"
                        :color="match ($status) { 'off' => 'gray', 'finished' => 'success', default => 'warning' }"
                    >
                        {{ match ($status) {
                            'off' => 'Off — she sees the countdown',
                            'intro' => 'On — she sees the intro',
                            'active' => 'On — Stop ' . $hunt->position . ' of ' . $stops->count(),
                            'finished' => 'Finished',
                        } }}
                    </x-filament::badge>
                </div>
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.7;">Points banked</div>
                    <div style="font-size: 1.875rem; font-weight: 700;">{{ $total }} / {{ $stops->count() * 10 }}</div>
                </div>
                @if ($current)
                    <div>
                        <div style="font-size: 0.875rem; opacity: 0.7;">Current stop</div>
                        <div style="font-size: 1.25rem; font-weight: 600;">{{ $current->title }}</div>
                        <div style="font-size: 0.875rem;">
                            {{ $current->clues_revealed >= 4 ? 'Answer revealed' : $current->clues_revealed . ' of 3 clues revealed' }}
                            · worth {{ $current->worth() }} pts right now
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section heading="Message under the countdown">
            @if (filled($hunt->message))
                <div style="white-space: pre-line;">{{ $hunt->message }}</div>
            @else
                <div style="opacity: 0.6;">None — nothing is shown. Use "Add message" above to set one.</div>
            @endif
        </x-filament::section>

        <x-filament::section heading="Stops">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach ($stops as $index => $stop)
                    <div style="display: flex; justify-content: space-between; gap: 1rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(128,128,128,0.2); {{ $current?->is($stop) ? 'font-weight: 700;' : '' }}">
                        <span>
                            {{ $stop->completed_at ? '✅' : ($current?->is($stop) ? '📍' : '⬜') }}
                            {{ $index + 1 }}. {{ $stop->title }}
                        </span>
                        <span style="white-space: nowrap;">
                            @if ($stop->completed_at)
                                +{{ $stop->points }} pts
                            @elseif ($current?->is($stop))
                                {{ $stop->clues_revealed >= 4 ? 'answer shown' : 'clue ' . $stop->clues_revealed }} · {{ $stop->worth() }} pts
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
