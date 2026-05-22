<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Fond extends Model
{
      protected $table = 'fondet';

      protected $fillable = [
          'fund_id',
          'company_id',
          'name',
          'funded_at',
          'raised_amount',
          'raised_currency_code',
          'source_url',
          'source_description',
          'verification_status',
          'verified_by',
          'verified_at',
          'verification_note',
      ];
    
    protected $casts = [
        'funded_at' => 'date',
        'raised_amount' => 'decimal:2',
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
