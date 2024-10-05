<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasFactory;
    protected $appends = ['urlImage'];

    public function getUrlImageAttribute()
    {
        if (
            isset($this->attributes['urlImage']) &&
            isset($this->attributes['urlImage'][0])
        ) {
            return url($this->attributes['urlImage']);
        }
    }
    protected $fillable = [
        'pet_id',
        'urlImage',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
