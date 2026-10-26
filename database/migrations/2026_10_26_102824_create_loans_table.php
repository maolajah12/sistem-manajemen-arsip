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
    Schema::create('loans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('archive_id')->constrained('archives')->cascadeOnDelete();
        $table->string('borrower_name');
        $table->string('division');
        $table->date('loan_date');
        $table->date('return_date')->nullable();
        $table->enum('status', ['Dipinjam', 'Dikembalikan'])->default('Dipinjam');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
