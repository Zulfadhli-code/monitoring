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
        Schema::create('history_photos', function (Blueprint $table) {

    $table->id();

    $table->foreignId('fasilitas_history_id')
        ->constrained()
        ->onDelete('cascade');

    $table->string('foto');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_photos');
    }
};
