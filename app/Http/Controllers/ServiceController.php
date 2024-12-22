<?php

namespace App\Http\Controllers;

use App\Models\{Service, Credit};
use App\Pipes\Filters\CategoryFilter;
use App\Pipes\Filters\MaxCostFilter;
use App\Pipes\Filters\MinCostFilter;
use App\Pipes\Filters\SearchFilter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Pipes\FilterServicesPipeline;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Pipeline;

class ServiceController extends Controller
{
  /**
     * Display a listing of the resource.
     */
    public function index()
    {


        return Inertia::render('Services/Index', [

        ]);
    }

    public function redeem(Request $request){

        $credits = Credit::find(1); // Assuming there's only one credit record

        // $filters = $request->only(['category', 'min_cost', 'max_cost', 'search']);

        $pipes = [
            new SearchFilter($request->get('search')),
            new CategoryFilter($request->get('category')),
            new MinCostFilter($request->get('min_cost')),
            new MaxCostFilter($request->get('max_cost')),
        ];
        $services = Pipeline::send(Service::query())
        ->through($pipes)
        ->thenReturn()
        ->get();

        return Inertia::render('Redeem',[
            'services'=> $services,
            'credits'=> $credits
        ]);

    }



    public function table(Request $request)
    {

        $sortDirection = $request->input('sortDirection', 'asc'); // default to 'asc' if not provided
        $sortField = $request->input('sortField', 'created_at'); // default to 'created_at' if not provided
        $limit = $request->input('limit', 15); // default to '15' if not provided

        // Query using the provided parameters, with fallbacks
        $records = Service::orderBy($sortField, $sortDirection)
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

        $query = Service::query();

        // Apply search filters to the query
        foreach ($fields as $field) {
            $query->orWhere($field, 'like', '%'.$search.'%');
        }

        // Limit the result to 10
        $records = $query->limit(10)->get();

        // Return the filtered Service as a JSON response
        return response()->json([
            'data' => $records,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Services/Create', [

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
        $item = new Service($validated);
        $item->save();

        return response()->json(['message' => 'Service saved successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return Inertia::render('Services/Show', [
            'item' => $service->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return Inertia::render('Services/Edit', [
            'item' => $service->toArray(), // Convert the $appointments model to an array
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([

        ]);

        // Assuming Measurement is your Eloquent model and you have a user_id column for the relation
        $service->fill($validated);
        $service->update();

        return response()->json(['message' => 'Service saved successfully', 'data' => $service], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
         $service->delete();

        return response()->json(['message' => 'Service Deleted'], 201);
    }
}
