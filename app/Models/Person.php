<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    protected $table = 'people';
    protected $guarded=[];
    protected $appends = ['image_url'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getImageUrlAttribute()
    {
        if (    
            isset($this->attributes['image_url']) &&
            isset($this->attributes['image_url'])
        ) {
            return url($this->attributes['image_url']);
        }
    }

}
