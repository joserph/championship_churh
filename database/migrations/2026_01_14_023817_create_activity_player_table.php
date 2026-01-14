<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_player', function (Blueprint $table) {
            $table->id();

            $table->foreignId('player_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('activity_id')
                ->constrained()
                ->cascadeOnDelete();

            // Week number (1–53) or any identifier you prefer
            $table->foreignId('championship_week_id')->constrained()->cascadeOnDelete();

            // Checkbox state
            $table->boolean('completed')->default(false);

            $table->timestamps();

            // Avoid duplicates for same player, activity and week
            $table->unique(
                ['player_id', 'activity_id', 'championship_week_id'],
                'activity_player_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_player');
    }
};
