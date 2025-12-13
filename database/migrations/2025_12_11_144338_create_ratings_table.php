<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Integer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating')->nullable()->default(0);
            $table->text('comment')->nullable();
            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignId('apartment_id')
            ->constrained('apartments')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->timestamps();
            $table->unique(['user_id', 'apartment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};