<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'points',
        'active',
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withPivot(['week', 'completed'])
            ->withTimestamps();
    }
}
