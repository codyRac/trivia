<?php

namespace App\Http\Controllers;

use App\Models\CreditUsed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreditUsedController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CreditUseds/Index', [

        ]);
    }

    public function table(Request $request)
    {

        $sortDirection = $request->input('sortDirection', 'asc'); // default to 'asc' if not provided
        $sortField = $request->input('sortField', 'created_at'); // default to 'created_at' if not provided
        $limit = $request->input('limit', 15); // default to '15' if not provided

        // Query using the provided parameters, with fallbacks
        $records = CreditUsed::orderBy($sortField, $sortDirection)
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

        $query = CreditUsed::query();

        // Apply search filters to the query
        foreach ($fields as $field) {
            $query->orWhere($field, 'like', '%'.$search.'%');
        }

        // Limit the result to 10
        $records = $query->limit(10)->get();

        // Return the filtered CreditUsed as a JSON response
        return response()->json([
            'data' => $records,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CreditUseds/Create', [

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
        $item = new CreditUsed($validated);
        $item->save();

        return response()->json(['message' => 'CreditUsed saved successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditUsed $creditUsed)
    {
        return Inertia::render('CreditUseds/Show', [
            'item' => $creditUsed->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditUsed $creditUsed)
    {
        return Inertia::render('CreditUseds/Edit', [
            'item' => $creditUsed->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditUsed $creditUsed)
    {
        $validated = $request->validate([

        ]);

        // Assuming Measurement is your Eloquent model and you have a user_id column for the relation
        $creditUsed->fill($validated);
        $creditUsed->update();

        return response()->json(['message' => 'CreditUsed saved successfully', 'data' => $creditUsed], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditUsed $creditUsed)
    {
         $creditUsed->delete();

        return response()->json(['message' => 'CreditUsed Deleted'], 201);
    }
}
