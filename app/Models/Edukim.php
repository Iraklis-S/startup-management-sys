<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edukim extends Model
{
        protected $table = 'edukimet';

        protected $fillable = [
        'person_id',
            'subject',
            'institution',
            'graduated_at',
        ];
    
    protected $casts = [
        'graduated_at' => 'date',
    ];
    
    public function personi()
    {
        return $this->belongsTo(Personi::class, 'person_id', 'id');
    }
}
