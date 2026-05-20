<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {

            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {

            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'keterangan',
                'updated_by'
            ]);
        });
    }
};