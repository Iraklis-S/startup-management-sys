<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Arritje extends Model
{
    protected $table = 'arritjet';

    protected $fillable = [
        'company_id',
        'milestone_at',
        'milestone_code',
        'source_url',
        'source_description',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];
    
    protected $casts = [
        'milestone_at' => 'date',
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
