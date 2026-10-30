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
        // Hanya eksekusi jika kolom file_path memang belum ada
        if (!Schema::hasColumn('archives', 'file_path')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->string('file_path')->nullable()->after('retention_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
