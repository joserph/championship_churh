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
        Schema::create('championship_standings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('championship_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('played_weeks')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('points_difference')->default(0);
            $table->integer('position')->nullable();

            $table->unique([
                'championship_id',
                'team_id'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championship_standings');
    }
};
