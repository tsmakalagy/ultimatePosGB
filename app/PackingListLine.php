<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingListLine extends Model
{
    protected $guarded = ['id'];
    
    public function transaction()
    {
        return $this->belongsTo(\App\PackingList::class, 'packing_list_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\ThePackage::class, 'the_package_id');
    }
}
