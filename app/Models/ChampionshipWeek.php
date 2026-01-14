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
        'is_active',
        'is_closed',
    ];

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }
}
