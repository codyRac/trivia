<?php

namespace App\Pipes\Filters;
use Illuminate\Database\Eloquent\Builder;

class MaxCostFilter
{
    public function __construct( private ? string $value){

    }


    public function __invoke(Builder $query, $next){

        if(! $this->value){
            return $next($query);
        }
        $query->where('cost', "<=", $this->value);

        return $next($query);

    }

}
