<?php

namespace App\Http\Controllers;

use App\Models\{Flag, FlagDay, Credit};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Random\Engine\Mt19937;
use Random\Randomizer;

class FlagController extends Controller
{
    const TIMEZONE = 'America/Los_Angeles';

    // Credits awarded for finishing the daily round
    const ROUND_CREDITS = 10;

    /**
     * Display the daily flag round.
     * Each day adds one new flag, then reviews every flag learned so far.
     */
    public function index()
    {
        $credits = Credit::find(1);
        $day = $this->today();
        $deck = $this->deck($day);

        return Inertia::render('Flags', [
            'credits' => $credits,
            'canAnswer' => !$day->completed_at && count($deck) > 0,
            'question' => $day->completed_at ? null : $this->question($day, $deck),
            'progress' => $this->progress($day, $deck),
            'newFlag' => $day->flag ? ['code' => $day->flag->code, 'name' => $day->flag->name] : null,
        ]);
    }

    /**
     * Handle an answer for the current flag in today's round.
     */
    public function answer(Request $request)
    {
        $request->validate([
            'flag_id' => 'required|exists:flags,id',
            'answer' => 'required|string',
        ]);

        $day = $this->today();
        $deck = $this->deck($day);

        if ($day->completed_at) {
            return Response::json(['message' => "You're done for today!"], 409);
        }

        // Only the current flag in the round can be answered
        if ((int) $request->flag_id !== $deck[$day->answered]) {
            return Response::json(['message' => 'That flag is out of order. Refresh the page.'], 409);
        }

        $flag = Flag::findOrFail($request->flag_id);
        $isCorrect = $flag->name === $request->answer;

        $flag->increment($isCorrect ? 'times_correct' : 'times_wrong');

        $day->answered += 1;
        $day->correct += $isCorrect ? 1 : 0;

        $credits = Credit::find(1);
        $message = $isCorrect ? '🎉 Correct!' : "Nope! That's {$flag->name}.";

        if ($day->answered >= count($deck)) {
            $day->completed_at = now();

            if ($credits) {
                $credits->credits += self::ROUND_CREDITS;
                $credits->earned += self::ROUND_CREDITS;
                $credits->update();
            }

            $message = "🏁 Round complete! {$day->correct}/" . count($deck) . ' correct. +' . self::ROUND_CREDITS . ' credits';
        }

        $day->save();

        return Response::json([
            'message' => $message,
            'correct' => $isCorrect,
            'correct_answer' => $flag->name,
            'next' => $day->completed_at ? null : $this->question($day, $deck),
            'progress' => $this->progress($day, $deck),
            'credits' => $credits?->credits,
            'earned' => $credits?->earned,
        ], 200);
    }

    /**
     * Get today's round, adding a new flag to the collection on the first visit of the day.
     */
    private function today(): FlagDay
    {
        $date = Carbon::today(self::TIMEZONE)->toDateString();

        $day = FlagDay::with('flag')->whereDate('date', $date)->first();
        if ($day) {
            return $day;
        }

        $newFlag = Flag::whereNull('learned_order')->inRandomOrder()->first();
        if ($newFlag) {
            $newFlag->update([
                'learned_order' => (Flag::max('learned_order') ?? 0) + 1,
                'learned_on' => $date,
            ]);
        }

        return FlagDay::create([
            'date' => $date,
            'flag_id' => $newFlag?->id,
            'answered' => 0,
            'correct' => 0,
        ])->load('flag');
    }

    /**
     * Ordered flag ids for the day: today's new flag first, then every earlier flag
     * shuffled. Seeded by the date so the order survives a page refresh.
     */
    private function deck(FlagDay $day): array
    {
        $learned = Flag::whereNotNull('learned_order')
            ->whereDate('learned_on', '<=', $day->date)
            ->orderBy('learned_order')
            ->pluck('id')
            ->reject(fn ($id) => $id === $day->flag_id)
            ->values()
            ->all();

        $review = $this->randomizer($day->date->toDateString())->shuffleArray($learned);

        return $day->flag_id ? [$day->flag_id, ...$review] : $review;
    }

    /**
     * The current flag with 4 shuffled country choices.
     */
    private function question(FlagDay $day, array $deck): ?array
    {
        $flagId = $deck[$day->answered] ?? null;
        if (!$flagId) {
            return null;
        }

        $flag = Flag::find($flagId);
        $randomizer = $this->randomizer($day->date->toDateString() . '-' . $flag->id);

        $others = Flag::where('id', '!=', $flag->id)->orderBy('id')->pluck('name')->all();
        $wrong = array_map(fn ($key) => $others[$key], $randomizer->pickArrayKeys($others, 3));

        return [
            'flag_id' => $flag->id,
            'code' => $flag->code,
            'is_new' => $flag->id === $day->flag_id,
            'answers' => $randomizer->shuffleArray([$flag->name, ...$wrong]),
        ];
    }

    private function progress(FlagDay $day, array $deck): array
    {
        return [
            'answered' => $day->answered,
            'correct' => $day->correct,
            'total' => count($deck),
            'learned' => Flag::whereNotNull('learned_order')->count(),
            'all' => Flag::count(),
        ];
    }

    private function randomizer(string $seed): Randomizer
    {
        return new Randomizer(new Mt19937(crc32($seed)));
    }
}
