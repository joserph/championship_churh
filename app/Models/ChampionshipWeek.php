<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChampionshipWeek extends Model
{
    protected $fillable = [
        'championship_id',
        'week_number',
        'start_date',
        'end_date',
        'active',
        'special_games_count'
    ];

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    public function specialGames()
    {
        return $this->hasMany(SpecialGame::class);
    }


}
