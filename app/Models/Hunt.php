<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Hunt extends Model
{
    // Points for a stop by how much was revealed: clue 1, clue 2, clue 3, the answer
    const POINTS = [10, 7, 4, 1];

    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create();
    }

    public function stops(): Collection
    {
        return HuntStop::orderBy('position')->orderBy('id')->get();
    }

    /**
     * off = not started, intro = welcome screen, active = on a stop, finished = all stops done
     */
    public function status(): string
    {
        return match (true) {
            $this->position === null => 'off',
            $this->position === 0 => 'intro',
            $this->position > HuntStop::count() => 'finished',
            default => 'active',
        };
    }

    public function currentStop(): ?HuntStop
    {
        if (! $this->position) {
            return null;
        }

        return $this->stops()->get($this->position - 1);
    }

    public function total(): int
    {
        return (int) HuntStop::whereNotNull('completed_at')->sum('points');
    }

    public function start(): void
    {
        if ($this->position !== null) {
            return;
        }

        $this->update(['position' => 0, 'started_at' => now()]);
    }

    /**
     * Bank the points for the current stop and move to the next stage.
     */
    public function advance(): void
    {
        if (in_array($this->status(), ['off', 'finished'])) {
            return;
        }

        if ($stop = $this->currentStop()) {
            $stop->update(['points' => $stop->worth(), 'completed_at' => now()]);
        }

        $this->update(['position' => $this->position + 1]);
    }

    /**
     * Undo the last advance. Clues already revealed stay revealed.
     */
    public function back(): void
    {
        if (! $this->position) {
            return;
        }

        $this->update(['position' => $this->position - 1]);
        $this->currentStop()?->update(['points' => null, 'completed_at' => null]);
    }

    /**
     * Turn the hunt off and wipe all progress.
     */
    public function reset(): void
    {
        HuntStop::query()->update(['clues_revealed' => 1, 'points' => null, 'completed_at' => null]);
        $this->update(['position' => null, 'started_at' => null]);
    }

    /**
     * Everything the player is allowed to see. Unrevealed clues never leave the server.
     */
    public function state(): array
    {
        $stops = $this->stops();
        $stop = $this->status() === 'active' ? $stops->get($this->position - 1) : null;

        return [
            'status' => $this->status(),
            'intro' => $this->status() === 'off' ? null : $this->intro,
            'total' => $this->total(),
            'maxTotal' => $stops->count() * self::POINTS[0],
            'stopNumber' => $stop ? $this->position : null,
            'stopCount' => $stops->count(),
            'stop' => $stop ? [
                'id' => $stop->id,
                'extra' => $stop->extra,
                'clues' => collect($stop->clues)
                    ->take(min($stop->clues_revealed, 3))
                    ->values()
                    ->map(fn ($text, $i) => ['text' => $text, 'points' => self::POINTS[$i]])
                    ->all(),
                'answer' => $stop->clues_revealed >= 4 ? $stop->answer : null,
                'worth' => $stop->worth(),
                'nextWorth' => self::POINTS[$stop->clues_revealed] ?? null,
                'nextIsAnswer' => $stop->clues_revealed === 3,
            ] : null,
            'completed' => $stops
                ->filter(fn ($s) => $s->completed_at)
                ->values()
                ->map(fn ($s, $i) => ['number' => $i + 1, 'title' => $s->title, 'points' => $s->points])
                ->all(),
        ];
    }
}
