<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class RaundiFinancimit extends Model
{
    protected $table = 'raundet_financimit';

    protected $fillable = [
        'funding_round_id',
        'company_id',
        'funded_at',
        'funding_round_type',
        'funding_round_code',
        'raised_amount_usd',
        'raised_amount',
        'raised_currency_code',
        'pre_money_valuation_usd',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'funded_at' => 'date',
        'raised_amount_usd' => 'decimal:2',
        'pre_money_valuation_usd' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'company_id');
    }

    public function investimet()
    {
        return $this->hasMany(Investim::class, 'funding_round_id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }
}
