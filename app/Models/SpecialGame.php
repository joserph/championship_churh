<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialGame extends Model
{
    protected $fillable = [
        'championship_week_id',
        'name',
        'points',
        'game_number'
    ];

    public function results()
    {
        return $this->hasMany(\App\Models\SpecialGameResult::class);
    }

    public function championshipWeek()
    {
        return $this->belongsTo(ChampionshipWeek::class);
    }

}
