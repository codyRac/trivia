<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagDay extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function flag()
    {
        return $this->belongsTo(Flag::class);
    }

}
