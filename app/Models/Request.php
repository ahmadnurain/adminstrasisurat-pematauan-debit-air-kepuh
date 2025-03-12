<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    protected $fillable = [
        'user_id',
        'request_status_id',
        'type_letter_id',
        'image_ktp',
        'image_selfie'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestStatus(): BelongsTo
    {
        return $this->belongsTo(RequestStatus::class);
    }

    public function typeLetter(): BelongsTo
    {
        return $this->belongsTo(TypeLetter::class);
    }

    public function domicileLetter(): HasOne
    {
        return $this->hasOne(DomicileLetter::class, 'request_id');
    }

    public function businessLetter(): HasOne
    {
        return $this->hasOne(BusinessLetter::class);
    }

    public function birthLetter(): HasOne
    {
        return $this->hasOne(BirthLetter::class);
    }

    public function villageLetter(): HasOne
    {
        return $this->hasOne(VillageLetter::class);
    }

    public function povertyLetter(): HasOne
    {
        return $this->hasOne(PovertyLetter::class, 'request_id');
    }

    public function incomeLetter(): HasOne
    {
        return $this->hasOne(IncomeLetter::class);
    }

    public function singleStatusLetter(): HasOne
    {
        return $this->hasOne(SingleStatusLetter::class);
    }

    public function maritalStatusLetter(): HasOne
    {
        return $this->hasOne(MaritalStatusLetter::class);
    }

    public function differentNameLetter(): HasOne
    {
        return $this->hasOne(DifferentNameLetter::class);
    }
    public function movingComesInOneVillageLetter()
    {
        return $this->hasOne(MovingComesInOneVillage::class, 'request_id', 'id');
    }
    public function movingBetweenVillages()
    {
        return $this->hasOne(MovingBetweenVillages::class, 'request_id', 'id');
    }
}
