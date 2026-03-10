<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamWeekScore extends Model
{
    protected $fillable = [
        'championship_week_id',
        'team_id',
        'total_points',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(ChampionshipWeek::class, 'championship_week_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
