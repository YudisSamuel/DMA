<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// Contoh file migrasi
public function up()
{
    Schema::table('tb_Peramalan', function (Blueprint $table) {
        $table->boolean('is_deleted')->default(false); // Menambahkan kolom is_deleted
    });
}

public function down()
{
    Schema::table('tb_Peramalan', function (Blueprint $table) {
        $table->dropColumn('is_deleted');
    });
}

};
