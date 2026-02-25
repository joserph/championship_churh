<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialGameResult extends Model
{
    protected $fillable = [
        'special_game_id',
        'team_id',
        'points_earned',
    ];

    public function specialGame()
    {
        return $this->belongsTo(\App\Models\SpecialGame::class);
    }

    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

}
