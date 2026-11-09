<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HuntStop extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'clues' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * What the stop is worth with the clues revealed so far.
     */
    public function worth(): int
    {
        return Hunt::POINTS[$this->clues_revealed - 1] ?? 1;
    }
}
