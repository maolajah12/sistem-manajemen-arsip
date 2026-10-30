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
        Schema::table('archives', function (Blueprint $table) {
            // Menambahkan kolom tingkat akses (Bebas / Terbatas)
            $table->enum('access_level', ['Bebas', 'Terbatas'])->default('Bebas')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn('access_level');
        });
    }
};
