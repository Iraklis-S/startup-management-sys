<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personi extends Model
{
    protected $table = 'personat';

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'birthplace',
        'affiliation_name',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_note',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'company_id', 'id');
    }

    public function marredheniet()
    {
        return $this->hasMany(Marredhenie::class, 'person_id', 'id');
    }

    public function edukimet()
    {
        return $this->hasMany(Edukim::class, 'person_id', 'id');
    }
}
