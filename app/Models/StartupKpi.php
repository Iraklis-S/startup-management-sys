<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StartupKpi extends Model
{
    protected $table = 'startup_kpis_view';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
