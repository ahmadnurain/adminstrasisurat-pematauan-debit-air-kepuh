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
        Schema::create('moving_between_villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained();
            $table->string('number_letter')->nullable();


            // Origin details
            $table->string('origin_family_card_number');
            $table->string('origin_head_of_family_name');
            $table->text('origin_address');
            $table->string('origin_rt');
            $table->string('origin_rw');
            $table->string('origin_hamlet')->nullable();
            $table->string('origin_village');
            $table->string('origin_district');
            $table->string('origin_regency');
            $table->string('origin_province');
            $table->string('origin_postal_code');
            $table->string('origin_phone')->nullable();

            // Applicant details
            $table->string('applicant_nik');
            $table->string('applicant_full_name');
            $table->string('moving_reason');

            // Destination details
            $table->text('destination_address');
            $table->string('destination_rt');
            $table->string('destination_rw');
            $table->string('destination_hamlet')->nullable();
            $table->string('destination_village');
            $table->string('destination_district');
            $table->string('destination_regency');
            $table->string('destination_province');
            $table->string('destination_postal_code');
            $table->string('destination_phone')->nullable();

            // Move type and family card status
            $table->string('moving_type');
            $table->string('kk_status_not_moving');
            $table->string('kk_status_moving');

            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moving_between_villages');
    }
};
