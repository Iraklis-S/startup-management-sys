<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roli extends Model
{
    protected $table = 'rolet';

    protected $fillable = [
        'role_name',
        'description',
    ];

    public function perdoruesit()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
