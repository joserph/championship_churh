<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChampionshipStanding extends Model
{
    protected $fillable = [
        'championship_id',
        'team_id',
        'played_weeks',
        'total_points',
        'points_difference',
        'position',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function championship(): BelongsTo
    {
        return $this->belongsTo(Championship::class);
    }
}
