<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Ipo extends Model
{
    protected $table = 'ipos';

    protected $fillable = [
        'company_id',
        'valuation_amount',
        'valuation_currency_code',
        'raised_amount',
        'raised_currency_code',
        'public_at',
        'stock_symbol',
        'source_url',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'public_at' => 'date',
        'verified_at' => 'datetime',
    ];

    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'company_id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }
}
