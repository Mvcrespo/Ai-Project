<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = "id";

    public $incrementing = true;
    public $timestamps = true;
    protected $keyType = "int";

    protected $fillable = ['title', 'genre_code', 'year', 'poster_filename', 'synopsis', 'trailer_url'];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'genre_code', 'code')->withTrashed();
    }

    public function screenings(): HasMany
    {
        return $this->hasMany(Screening::class);
    }

    public function getPosterFullUrlAttribute()
    {
        if ($this->poster_filename && Storage::exists("public/posters/{$this->poster_filename}")) {
            return asset("storage/posters/{$this->poster_filename}");
        } else {
            return asset("img/default_poster.jpg");
        }
    }

    public function getTrailerEmbedUrlAttribute()
    {
        if (str_contains($this->trailer_url, 'watch?v=')) {
            return str_replace('watch?v=', 'embed/', $this->trailer_url);
        } else {
            return $this->trailer_url;
        }
    }
}
