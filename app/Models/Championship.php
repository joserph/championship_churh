<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'active',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    
}
