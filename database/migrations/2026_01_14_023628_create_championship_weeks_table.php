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
        Schema::create('championship_weeks', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('championship_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('week_number');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('active')->default(true);
            $table->integer('special_games_count')->default(3);

            $table->timestamps();

            $table->unique(['championship_id', 'week_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championship_weeks');
    }
};
