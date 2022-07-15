<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageTransactionLine extends Model
{
    protected $guarded = ['id'];
        public function package_transaction()
    {
        return $this->belongsTo(\App\PackageTransaction::class);
    }

    public function package()
    {
        return $this->belongsTo(\App\Package::class, 'package_id');
    }
}
