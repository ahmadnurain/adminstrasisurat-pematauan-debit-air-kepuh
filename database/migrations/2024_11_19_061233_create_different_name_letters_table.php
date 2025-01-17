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
        Schema::create('different_name_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained(); // Foreign key linking to the requests table
            $table->string('number_letter')->nullable(); // Optional letter number
            $table->string('name'); // Name of the applicant
            $table->string('same_name'); // Name of the applicant
            $table->string('nik'); // National Identity Number
            $table->enum('gender', ['Laki-Laki', 'Perempuan']); // Gender of the applicant
            $table->string('birth_place'); // Place of birth
            $table->date('birth_date'); // Date of birth
            $table->string('nationality'); // Nationality
            $table->string('religion'); // Religion
            $table->string('occupation'); // Occupation of the applicant
            $table->text('address'); // Address of the applicant
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('different_name_letters');
    }
};
