<?php

namespace App\Pipes;
use App\Pipes\Filters\SearchFilter;
use Closure;

class FilterServicesPipeline
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function apply($query, $filters)
    {

        $pipes = [

            new SearchFilter($request->get('search'))

        ];

        return app(\Illuminate\Pipeline\Pipeline::class)
            ->send($query)
            ->through([
                // Filter by category
                function ($query) use ($filters) {
                    if (!empty($filters['category'])) {
                        $query->where('category', $filters['category']);
                    }
                    return $query;
                },
                // Filter by cost range
                function ($query) use ($filters) {
                    if (!empty($filters['min_cost'])) {
                        $query->where('cost', '>=', $filters['min_cost']);
                    }
                    if (!empty($filters['max_cost'])) {
                        $query->where('cost', '<=', $filters['max_cost']);
                    }
                    return $query;
                },
                // Search by title
                function ($query) use ($filters) {
                    if (!empty($filters['search'])) {
                        $query->where('title', 'like', '%' . $filters['search'] . '%');
                    }
                    return $query;
                },
            ])
            ->thenReturn();
    }
}
