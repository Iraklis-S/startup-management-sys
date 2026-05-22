<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikim;

class Kompania extends Model
{
    protected $table = 'kompanite';

    protected $fillable = [
        'company_type',
        'parent_id',
        'name',
        'normalized_name',
        'permalink',
        'category_code',
        'status',
        'founded_at',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    protected $casts = [
        'founded_at' => 'date',
        'verified_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Kompania::class, 'parent_id');
    }

    public function subsidiaries()
    {
        return $this->hasMany(Kompania::class, 'parent_id');
    }

    public function raundetFinancimit()
    {
        return $this->hasMany(RaundiFinancimit::class, 'company_id');
    }

    public function investimetFunded()
    {
        return $this->hasMany(Investim::class, 'funded_company_id');
    }

    public function investimetInvestor()
    {
        return $this->hasMany(Investim::class, 'investor_company_id');
    }

    public function fondet()
    {
        return $this->hasMany(Fond::class, 'company_id');
    }

    public function blerjetSiBlerese()
    {
        return $this->hasMany(Blerje::class, 'acquiring_company_id');
    }

    public function blerjetSiEBlere()
    {
        return $this->hasMany(Blerje::class, 'acquired_company_id');
    }

    public function ipos()
    {
        return $this->hasMany(Ipo::class, 'company_id');
    }

    public function arritjet()
    {
        return $this->hasMany(Arritje::class, 'company_id');
    }

    public function zyrat()
    {
        return $this->hasMany(Zyra::class, 'company_id');
    }

    public function marredheniet()
    {
        return $this->hasMany(Marredhenie::class, 'company_id');
    }

    public function personi()
    {
        return $this->hasOne(Personi::class, 'company_id', 'id');
    }

    public function personat()
    {
        return $this->belongsToMany(Personi::class, 'marredheniet', 'company_id', 'person_id', 'id', 'id');
    }

    public function verifikime()
    {
        return $this->morphMany(Verifikim::class, 'verifiable');
    }
}
