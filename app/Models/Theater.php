<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theater extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'photo_filename',
    ];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
