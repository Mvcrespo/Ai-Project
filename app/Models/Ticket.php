<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['screening_id','seat_id','purchase_id','price','qrcode_url','status'];

    public function getQrCodeFileAttribute()
     {
         if ($this->qrcode_url && Storage::exists("ticket_qrcodes/{$this->qrcode_url}")) {
             return "ticket_qrcodes/".$this->receipt_pdf_filename;
         } else {
             return "";
         }
     }


    public function purchase():BelongsTo
    {
        return $this->belongsTo(Theater::class());

    }

    public function screening():BelongsTo
    {
        return $this->belongsTo(Screening::class());

    }

    public function seat():BelongsTo
    {
        return $this->belongsTo(Seat::class())->withTrashed();

    }
}
