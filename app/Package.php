<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
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

    //    public function package_thepackage()
    // {
    //     return $this->belongsToMany(\App\ThePackage::class, 'thePackage_packages', 'package_id', 'the_package_id');
    // }
}
