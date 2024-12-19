<?php

namespace App\Pipes\Filters;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function __construct( private ? string $value){

    }


    public function __invoke(Builder $query, $next){

        if(! $this->value){
            return $next($query);
        }
        $query->whereLike('title', "%$this->value%");

        return $next($query);

    }

}
