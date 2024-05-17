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
    protected $fillable =['customer_id', 'date','total_price','customer_name','customer_email', 'nif',
     'payment_type','payment_ref', 'receipt_pdf_filename'];

     public function getReceiptPdfFilenameAttribute()
     {
         if ($this->receipt_pdf_filename && Storage::exists("pdf_purchases/{$this->receipt_pdf_filename}")) {
             return "pdf_purchases/".$this->receipt_pdf_filename;
         } else {
             return "";
         }
     }

     public function customer():HasOne
     {
        return $this->hasOne(Customer::class)->withTrashed();
     }

     public function tickets():HasMany
     {
        return $this->hasMany(Ticket::class());
     }
}
