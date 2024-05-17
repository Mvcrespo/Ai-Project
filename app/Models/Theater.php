<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Theater extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamp = false;

    protected $fillable=['name','photo_filename'];



    public function screenings():HasMany
    {
        return $this->hasMany(Screening::class());

    }

    public function seats():HasMany
    {
        return $this->hasMany(Seat::class());

    }

    public function getTheaterFullUrlAttribute()
    {
        if ($this->photo_filename && Storage::exists("public/photos_theaters/{$this->photo_filename}")) {
            return asset("storage/photos_theaters/{$this->photo_filename}");
        } else {
            return asset("img/default_theater.jpg");
        }
    }
}
