<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamWeekScore extends Model
{
    protected $fillable = [
        'championship_week_id',
        'team_id',
        'total_points',
    ];
}
