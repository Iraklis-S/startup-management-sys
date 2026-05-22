<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyRelationship extends Model
{
    protected $table = 'company_relationships_view';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];
}
