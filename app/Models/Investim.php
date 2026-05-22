<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Investim extends Model
{
    protected $table = 'investimet';

    protected $fillable = [
        'funding_round_id',
        'funded_company_id',
        'investor_company_id',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    public $timestamps = true;

    public function raundiFinancimit()
    {
        return $this->belongsTo(RaundiFinancimit::class, 'funding_round_id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }

    public function fundedKompania()
    {
        return $this->belongsTo(Kompania::class, 'funded_company_id');
    }

    public function investorKompania()
    {
        return $this->belongsTo(Kompania::class, 'investor_company_id');
    }
}
