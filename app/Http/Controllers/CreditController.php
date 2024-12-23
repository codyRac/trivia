<?php

namespace App\Http\Controllers;

use App\Models\{Credit, Service, CreditUsed};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CreditController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Credits/Index', [

        ]);
    }

    public function useCredits(Request $request)
{
    $request->validate([
        'service_id' => 'required|exists:services,id',
    ]);

    $userCredits = Credit::find(1); // Replace with actual user credit lookup
    $service = Service::find($request->service_id);

    if ($userCredits->credits < $service->cost) {
        return response()->json(['success' => false, 'message' => 'Not enough credits.'], 400);
    }

    $cost =$service->cost;
    // Deduct credits
    $userCredits->credits -= $service->cost;
    $userCredits->spent += $service->cost;
    $userCredits->update();
    CreditUsed::create(
        [
            'service_id'=>$service->id,
            'credits'=> $cost,
            'date_used'=> Carbon::now()

        ]
    );
    $service->times_used++;
    $service->cost += 15;
    $service->update();

    return response()->json([
        'success' => true,
        'message' => 'Service redeemed successfully!',
        'remaining_credits' => $userCredits->credits,
    ], 200);
}

    public function table(Request $request)
    {

        $sortDirection = $request->input('sortDirection', 'asc'); // default to 'asc' if not provided
        $sortField = $request->input('sortField', 'created_at'); // default to 'created_at' if not provided
        $limit = $request->input('limit', 15); // default to '15' if not provided

        // Query using the provided parameters, with fallbacks
        $records = Credit::orderBy($sortField, $sortDirection)
            ->paginate($limit);

        // Return the appointments as a JSON response
        return response()->json([
            'data' => $records,
        ]);
    }

    public function list(Request $request)
    {

        $search = $request->input('search', ''); // Get the search query
        $fields = ['name', 'type']; // Fields to search in

        $query = Credit::query();

        // Apply search filters to the query
        foreach ($fields as $field) {
            $query->orWhere($field, 'like', '%'.$search.'%');
        }

        // Limit the result to 10
        $records = $query->limit(10)->get();

        // Return the filtered Credit as a JSON response
        return response()->json([
            'data' => $records,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Credits/Create', [

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
        $item = new Credit($validated);
        $item->save();

        return response()->json(['message' => 'Credit saved successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Credit $credit)
    {
        return Inertia::render('Credits/Show', [
            'item' => $credit->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Credit $credit)
    {
        return Inertia::render('Credits/Edit', [
            'item' => $credit->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Credit $credit)
    {
        $validated = $request->validate([

        ]);

        // Assuming Measurement is your Eloquent model and you have a user_id column for the relation
        $credit->fill($validated);
        $credit->update();

        return response()->json(['message' => 'Credit saved successfully', 'data' => $credit], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Credit $credit)
    {
         $credit->delete();

        return response()->json(['message' => 'Credit Deleted'], 201);
    }
}
