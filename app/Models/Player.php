<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name', 'birthdate', 'phone', 'email', // Changed from 'age' to 'birthdate'
        'team_id', 'active'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    protected $casts = [
        'birthdate' => 'date', // Add date casting
    ];

    // CALCULATED ATTRIBUTE: Calculate age from birthdate
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    // public function activities()
    // {
    //     return $this->belongsToMany(Activity::class)
    //         ->withPivot(['week', 'completed'])
    //         ->withTimestamps();
    // }

    // // Points per week
    // public function pointsForWeek(int $week): int
    // {
    //     return $this->activities()
    //         ->wherePivot('week', $week)
    //         ->wherePivot('completed', true)
    //         ->sum('points');
    // }

    public function activities()
    {
        return $this->belongsToMany(Activity::class)
            ->withPivot(['championship_week_id', 'completed'])
            ->withTimestamps();
    }

    public function pointsForWeek(int $weekId): int
    {
        return $this->activities()
            ->wherePivot('championship_week_id', $weekId)
            ->wherePivot('completed', true)
            ->sum('points');
    }

    function activeWeekForChampionship(int $championshipId)
    {
        return ChampionshipWeek::where('championship_id', $championshipId)
            ->where('is_active', true)
            ->first();
    }
}
