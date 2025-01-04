<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $appends = ['image_url'];

    protected $guarded=[];
    public function getImageUrlAttribute()
    {
        if (
            isset($this->attributes['image_url']) &&
            isset($this->attributes['image_url'][0])
        ) {
            return url($this->attributes['image_url']);
        }
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
