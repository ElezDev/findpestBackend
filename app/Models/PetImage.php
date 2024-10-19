<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (
            isset($this->attributes['image_url']) &&
            isset($this->attributes['image_url'][0])
        ) {
            return url($this->attributes['image_url']);
        }
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
