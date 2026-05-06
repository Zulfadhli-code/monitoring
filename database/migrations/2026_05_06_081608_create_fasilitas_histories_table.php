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
        Schema::create('fasilitas_histories', function (Blueprint $table) {
    $table->id();

    $table->foreignId('fasilitas_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->string('status_from')->nullable();
    $table->string('status_to');

    $table->timestamps();

    $table->index(['fasilitas_id', 'created_at']); // 
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_histories');
    }
};
