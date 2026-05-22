<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Marredhenie extends Model
{
    protected $table = 'marredheniet';

    protected $fillable = [
        'person_id',
        'company_id',
        'start_at',
        'end_at',
        'is_past',
        'sequence',
        'title',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'is_past' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function personi()
    {
        return $this->belongsTo(Personi::class, 'person_id');
    }

    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'company_id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }
}
