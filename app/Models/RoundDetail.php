<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoundDetail extends Model
{
    protected $table = 'round_detail_view';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
