<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerActivityWeek extends Model
{
    protected $fillable = [
        'championship_week_id',
        'player_id',
        'activity_id',
        'points_earned'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function week()
    {
        return $this->belongsTo(ChampionshipWeek::class, 'championship_week_id');
    }
}
