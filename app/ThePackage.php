<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThePackage extends Model
{
    
    protected $guarded=["id"];

    protected $appends = ['image_url'];

        /**
     * Get the products image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url = asset('/uploads/img/' . rawurlencode($this->image));
        } else {
            $image_url = asset('/img/default.png');
        }
        return $image_url;
    }

    /**
    * Get the products image path.
    *
    * @return string
    */
    public function getImagePathAttribute()
    {
        if (!empty($this->image)) {
            $image_path = public_path('uploads') . '/' . config('constants.product_img_path') . '/' . $this->image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }

        public function thepackage_package()
    {
        return $this->belongsToMany(\App\Package::class, 'thePackage_packages', 'the_package_id', 'package_id');
    }

    public function getBarcodeAttribute()
    {
        $created_at=$this->created_at->format('Y-m-d H:i');
        $id=str_pad($this->id, 4, '0', STR_PAD_LEFT);
        $barcode='the_package-'.$id.'-'.$created_at;
        return $barcode;
    }
}
