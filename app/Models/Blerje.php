<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Blerje extends Model
{
        protected $table = 'blerjet';

        protected $fillable = [
            'acquisition_id',
            'acquiring_company_id',
            'acquired_company_id',
            'term_code',
            'price_amount',
            'price_currency_code',
            'acquired_at',
            'source_url',
            'source_description',
            'verification_status',
            'verified_by',
            'verified_at',
            'verification_note',
        ];
    
    protected $casts = [
        'acquired_at' => 'date',
        'price_amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];
    
    public function acquiringKompania()
    {
        return $this->belongsTo(Kompania::class, 'acquiring_company_id');
    }
    
    public function acquiredKompania()
    {
        return $this->belongsTo(Kompania::class, 'acquired_company_id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }
}
