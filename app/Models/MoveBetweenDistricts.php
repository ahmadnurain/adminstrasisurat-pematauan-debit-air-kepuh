<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoveBetweenDistricts extends Model
{
    //
    use HasFactory;

    protected $table = 'move_between_sub_districts';

    protected $fillable = [
        'province',
        'province_code',
        'regency',
        'regency_code',
        'district',
        'district_code',
        'village',
        'village_code',
        'hamlet',
        'origin_family_card_number',
        'origin_head_of_family_name',
        'origin_address',
        'origin_rt',
        'origin_rw',
        'origin_hamlet',
        'origin_village',
        'origin_district',
        'origin_regency',
        'origin_province',
        'origin_postal_code',
        'origin_phone',
        'applicant_nik',
        'applicant_full_name',
        'moving_reason',
        'destination_address',
        'destination_rt',
        'destination_rw',
        'destination_hamlet',
        'destination_village',
        'destination_district',
        'destination_regency',
        'destination_province',
        'destination_postal_code',
        'destination_phone',
        'moving_type',
        'kk_status_not_moving',
        'kk_status_moving',
    ];
    public function family()
    {
        return $this->hasMany(Family::class, 'request_id');  // Relasi ke tabel families
    }
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}
