<?php

namespace App\Pipes\Filters;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilter
{
    public function __construct( private ? string $value){

    }


    public function __invoke(Builder $query, $next){

        if(! $this->value){
            return $next($query);
        }
        $query->where('category', $this->value);

        return $next($query);

    }

}
