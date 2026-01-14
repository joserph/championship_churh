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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();

            $table->foreignId('championship_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->default('#3b82f6');
            $table->string('light_color')->default('#dbeafe');
            $table->string('dark_color')->default('#1e40af');
            $table->string('icon')->default('heroicon-o-user-group');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
