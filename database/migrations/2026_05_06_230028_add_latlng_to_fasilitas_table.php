<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::table('fasilitas', function (Blueprint $table) {
        if (!Schema::hasColumn('fasilitas', 'latitude')) {
            $table->decimal('latitude', 10, 7)->nullable();
        }

        if (!Schema::hasColumn('fasilitas', 'longitude')) {
            $table->decimal('longitude', 10, 7)->nullable();
        }
    });
}

    public function down(): void
    {
        Schema::table('fasilitas', function ($table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};