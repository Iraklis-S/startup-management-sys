<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Verifikim extends Model
{
    protected $table = 'verifikime';

    protected $fillable = [
        'verifiable_type',
        'verifiable_id',
        'action',
        'note',
        'verified_by',
    ];

    public $timestamps = false;

    public function verifiable()
    {
        return $this->morphTo();
    }

    public function verifikues()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
