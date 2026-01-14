<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'championship_id',
        'name', 'color', 'light_color', 'dark_color',
        'icon', 'description', 'active'
    ];

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    // public function players()
    // {
    //     return $this->hasMany(Player::class);
    // }
}
