<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = [];

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }
}
