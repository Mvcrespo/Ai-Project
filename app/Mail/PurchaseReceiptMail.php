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
    public $pdfReceiptPath;
    public $pdfTicketsContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($purchase, $pdfReceiptPath, $pdfTicketsContent)
    {
        $this->purchase = $purchase;
        $this->pdfReceiptPath = $pdfReceiptPath;
        $this->pdfTicketsContent = $pdfTicketsContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.purchase_receipt')
                    ->attach($this->pdfReceiptPath, [
                        'as' => 'receipt.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->attachData($this->pdfTicketsContent, 'tickets.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
