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
}
