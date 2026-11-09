<?php

namespace App\Http\Controllers;

use App\Models\Hunt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class HuntController extends Controller
{
    public function index()
    {
        $hunt = Hunt::current();

        if ($hunt->status() === 'off') {
            return redirect()->route('holding');
        }

        return Inertia::render('Hunt', ['hunt' => $hunt->state()]);
    }

    /**
     * Polled by the hunt and holding pages so they update when the hunt is started or advanced.
     */
    public function state()
    {
        return Response::json(Hunt::current()->state());
    }

    /**
     * Reveal the next clue (or the answer) for the current stop, lowering what it is worth.
     */
    public function reveal(Request $request)
    {
        $request->validate(['stop_id' => 'required|integer']);

        $hunt = Hunt::current();
        $stop = $hunt->currentStop();

        // The stop can change under her if it gets advanced while she is tapping
        if (! $stop || $stop->completed_at || $stop->id !== (int) $request->stop_id) {
            return Response::json(['message' => 'The hunt has moved on!', 'hunt' => $hunt->state()], 409);
        }

        if ($stop->clues_revealed < count(Hunt::POINTS)) {
            $stop->increment('clues_revealed');
        }

        return Response::json(['hunt' => $hunt->state()]);
    }
}
