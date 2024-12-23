<?php

namespace App\Http\Controllers;

use App\Models\{Trivia, Credit};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class TriviaController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $credits =  Credit::find(1);
        // Check if there is a trivia used today
        $timezone = 'America/Los_Angeles';

        $todayStartLA = Carbon::today($timezone);
        $todayStartUTC = $todayStartLA->copy()->setTimezone('UTC');

        // Check if there is a trivia used today based on UTC time
        $hasAnsweredToday = Trivia::where('used_on', '>=', $todayStartUTC)->exists();

        // If trivia has already been answered today, set canAnswer to false
        if ($hasAnsweredToday) {
            return Inertia::render('Trivia', [
                'credits' => $credits,
                'canAnswer' => false,
                'trivia' => null,
            ]);
        }
        $trivia = Trivia::where('used', false)->inRandomOrder()->first(); // Fetch a random trivia record

         // Combine correct answer and wrong answers, then shuffle
        $answers = collect([
            ['id' => 1, 'text' => $trivia->answer, 'is_correct' => true],
            ['id' => 2, 'text' => $trivia->wrong_1, 'is_correct' => false],
            ['id' => 3, 'text' => $trivia->wrong_2, 'is_correct' => false],
            ['id' => 4, 'text' => $trivia->wrong_3, 'is_correct' => false],
        ])->shuffle();
        return Inertia::render('Trivia', [
            'credits'=> $credits,
            'canAnswer'=> true,
            'trivia' => [
                'id'=> $trivia->id,
                'question' => $trivia->question,
                'category' => $trivia->category,
                'answers' => $answers,
            ]
        ]);
    }

    public function answered(Request $request){

        $request->validate([
            'trivia_id' => 'required|exists:trivia,id', // Ensure trivia_id exists in the database
            'answer' => 'required|string', // Ensure answer is provided as a string
        ]);

        // Fetch the trivia record
        $trivia = Trivia::findOrFail($request->trivia_id);

        // If the trivia doesn't exist, throw an error (handled by `findOrFail`)
        if (!$trivia) {
            return Response::json(['message' => 'Trivia not found.'], 404);
        }

        // Check if the provided answer matches the correct answer
        $message = 'Wrong answer.';
        $result = 'wrong';

        $credits = Credit::find(1); // Assuming there's only one credit record


        if($trivia->answer == $request->answer){
            $message = 'You Got it Right!';
            $credits = Credit::find(1);
            $credits->credits +=10;
            $credits->earned +=10;
            $credits->update();
            $result = 'right';
        }

        $trivia->used = 1;
        $trivia->used_on = Carbon::now();
        $trivia->result = $result;
        $trivia->update();

        // Send the response back with the message and updated credits
        return Response::json([
            'message' => $message,
            'credits' => $credits->credits,
            'earned' => $credits->earned,
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Trivias/Create', [

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

        ]);


        // Assuming Measurement is your Eloquent model and you have a user_id column for the relation
        $item = new Trivia($validated);
        $item->save();

        return response()->json(['message' => 'Trivia saved successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trivia $trivia)
    {
        return Inertia::render('Trivias/Show', [
            'item' => $trivia->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trivia $trivia)
    {
        return Inertia::render('Trivias/Edit', [
            'item' => $trivia->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trivia $trivia)
    {
        $validated = $request->validate([

        ]);

        // Assuming Measurement is your Eloquent model and you have a user_id column for the relation
        $trivia->fill($validated);
        $trivia->update();

        return response()->json(['message' => 'Trivia saved successfully', 'data' => $trivia], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trivia $trivia)
    {
         $trivia->delete();

        return response()->json(['message' => 'Trivia Deleted'], 201);
    }
}
