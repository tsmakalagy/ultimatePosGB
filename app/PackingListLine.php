<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingListLine extends Model
{
    protected $guarded = ['id'];
    
    public function packinglist()
    {
        return $this->belongsTo(\App\PackingList::class);
    }

    public function thepackage()
    {
        return $this->belongsTo(\App\ThePackage::class, 'the_package_id');
    }
}
