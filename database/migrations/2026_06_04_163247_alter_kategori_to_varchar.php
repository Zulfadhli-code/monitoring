<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('fasilitas', function (Blueprint $table) {
        $table->string('kategori', 255)->change();
        $table->string('nama', 255)->change();
        $table->string('lokasi', 255)->change();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('varchar', function (Blueprint $table) {
            //
        });
    }
};
