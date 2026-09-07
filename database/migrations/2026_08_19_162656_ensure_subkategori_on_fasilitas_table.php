<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('fasilitas', 'subkategori')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->string('subkategori')->nullable()->after('kategori');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('fasilitas', 'subkategori')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->dropColumn('subkategori');
            });
        }
    }
};