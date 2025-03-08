<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeathCertificate extends Model
{
    use HasFactory;

    protected $table = 'death_certificates';

    protected $fillable = [
        'head_of_family_name',
        'head_of_family_number',
        'deceased_nik',
        'deceased_full_name',
        'deceased_gender',
        'deceased_birth_date',
        'deceased_age',
        'deceased_birth_place',
        'deceased_religion',
        'deceased_occupation',
        'deceased_address',
        'deceased_village',
        'deceased_district',
        'deceased_regency',
        'deceased_province',
        'deceased_child_number',
        'death_date',
        'death_time',
        'cause_of_death',
        'death_place',
        'certifier',
        'father_nik',
        'father_full_name',
        'father_birth_date',
        'father_age',
        'father_occupation',
        'father_address',
        'father_village',
        'father_district',
        'father_regency',
        'father_province',
        'mother_nik',
        'mother_full_name',
        'mother_birth_date',
        'mother_age',
        'mother_occupation',
        'mother_address',
        'mother_village',
        'mother_district',
        'mother_regency',
        'mother_province',
        'reporter_nik',
        'reporter_full_name',
        'reporter_birth_date',
        'reporter_age',
        'reporter_occupation',
        'reporter_address',
        'reporter_village',
        'reporter_district',
        'reporter_regency',
        'reporter_province',
        'witness1_nik',
        'witness1_full_name',
        'witness1_birth_date',
        'witness1_age',
        'witness1_occupation',
        'witness1_address',
        'witness1_village',
        'witness1_district',
        'witness1_regency',
        'witness1_province',
        'witness2_nik',
        'witness2_full_name',
        'witness2_birth_date',
        'witness2_age',
        'witness2_occupation',
        'witness2_address',
        'witness2_village',
        'witness2_district',
        'witness2_regency',
        'witness2_province',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}
