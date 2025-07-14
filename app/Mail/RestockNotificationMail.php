<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestockNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $stockRequest;
    public $medicine;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($stockRequest, $medicine)
    {
        $this->stockRequest = $stockRequest;
        $this->medicine = $medicine;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.restock-notification')
                    ->subject('Stock Request Fulfilled - Medicine Available')
                    ->with([
                        'stockRequest' => $this->stockRequest,
                        'medicine' => $this->medicine,
                        'pharmacist' => $this->stockRequest->pharmacist,
                        'supplier' => $this->stockRequest->supplier,
                    ]);
    }
}