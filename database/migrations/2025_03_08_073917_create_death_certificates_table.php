<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('death_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained();
            $table->string('number_letter')->nullable();
            $table->string('head_of_family_name');
            $table->string('head_of_family_number');
            $table->string('deceased_nik');
            $table->string('deceased_full_name');
            $table->string('deceased_gender');
            $table->date('deceased_birth_date');
            $table->integer('deceased_age');
            $table->string('deceased_birth_place');
            $table->string('deceased_religion');
            $table->string('deceased_occupation');
            $table->text('deceased_address');
            $table->string('deceased_village');
            $table->string('deceased_district');
            $table->string('deceased_regency');
            $table->string('deceased_province');
            $table->integer('deceased_child_number');

            $table->date('death_date');
            $table->time('death_time');
            $table->string('cause_of_death');
            $table->string('death_place');
            $table->string('certifier');

            $table->string('father_nik');
            $table->string('father_full_name');
            $table->date('father_birth_date');
            $table->integer('father_age');
            $table->string('father_occupation');
            $table->text('father_address');
            $table->string('father_village');
            $table->string('father_district');
            $table->string('father_regency');
            $table->string('father_province');

            $table->string('mother_nik');
            $table->string('mother_full_name');
            $table->date('mother_birth_date');
            $table->integer('mother_age');
            $table->string('mother_occupation');
            $table->text('mother_address');
            $table->string('mother_village');
            $table->string('mother_district');
            $table->string('mother_regency');
            $table->string('mother_province');

            $table->string('reporter_nik');
            $table->string('reporter_full_name');
            $table->date('reporter_birth_date');
            $table->integer('reporter_age');
            $table->string('reporter_occupation');
            $table->text('reporter_address');
            $table->string('reporter_village');
            $table->string('reporter_district');
            $table->string('reporter_regency');
            $table->string('reporter_province');

            $table->string('witness1_nik');
            $table->string('witness1_full_name');
            $table->date('witness1_birth_date');
            $table->integer('witness1_age');
            $table->string('witness1_occupation');
            $table->text('witness1_address');
            $table->string('witness1_village');
            $table->string('witness1_district');
            $table->string('witness1_regency');
            $table->string('witness1_province');

            $table->string('witness2_nik');
            $table->string('witness2_full_name');
            $table->date('witness2_birth_date');
            $table->integer('witness2_age');
            $table->string('witness2_occupation');
            $table->text('witness2_address');
            $table->string('witness2_village');
            $table->string('witness2_district');
            $table->string('witness2_regency');
            $table->string('witness2_province');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('death_certificates');
    }
};
