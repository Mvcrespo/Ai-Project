<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false; // Corrigido aqui para timestamps

    protected $fillable = ['theater_id', 'row', 'seat_number'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class); // Removido parênteses
    }

    public function theater(): BelongsTo
    {
        return $this->belongsTo(Theater::class)->withTrashed(); // Removido parênteses
    }
}
