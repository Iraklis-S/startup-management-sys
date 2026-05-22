<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorHistory extends Model
{
    protected $table = 'investor_history_view';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
