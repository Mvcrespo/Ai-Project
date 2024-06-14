<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;
    public $pdfPath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase, $pdfPath)
    {
        $this->purchase = $purchase;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.purchase_receipt')
                    ->subject('Your Purchase Receipt')
                    ->attach($this->pdfPath, [
                        'as' => 'receipt.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
