<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zyra extends Model
{
      protected $table = 'zyrat';

      protected $fillable = [
          'company_id',
          'office_id',
          'description',
          'region',
          'city',
          'zip_code',
          'latitude',
          'longitude',
      ];
    
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
    
    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'company_id');
    }
}
