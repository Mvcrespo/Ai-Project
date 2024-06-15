<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'date', 'total_price', 'customer_name', 'customer_email', 'nif',
        'payment_type', 'payment_ref', 'receipt_pdf_filename'
    ];

    protected $dates = ['date', 'created_at', 'updated_at'];

    public function getReceiptPdfFilenameAttribute()
    {
        if ($this->attributes['receipt_pdf_filename'] && Storage::exists("pdf_purchases/{$this->attributes['receipt_pdf_filename']}")) {
            return "pdf_purchases/".$this->attributes['receipt_pdf_filename'];
        } else {
            return "";
        }
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}


