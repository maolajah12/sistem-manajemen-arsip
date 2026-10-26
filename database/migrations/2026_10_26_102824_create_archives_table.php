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
    Schema::create('archives', function (Blueprint $table) {
        $table->id();
        $table->string('document_number')->unique();
        $table->string('title');
        $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
        $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
        $table->string('file_path')->nullable();
        $table->date('retention_date')->nullable();
        $table->enum('status', ['Tersedia', 'Dipinjam', 'Kedaluwarsa'])->default('Tersedia');
        $table->softDeletes();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
