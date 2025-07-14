<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $medicine;
    public $pharmacist;
    public $supplier;
    public $requestedQuantity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($medicine, $pharmacist, $supplier, $requestedQuantity = null)
    {
        $this->medicine = $medicine;
        $this->pharmacist = $pharmacist;
        $this->supplier = $supplier;
        $this->requestedQuantity = $requestedQuantity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.stock-alert')
                    ->subject('Stock Alert - Restock Required')
                    ->with([
                        'medicine' => $this->medicine,
                        'pharmacist' => $this->pharmacist,
                        'supplier' => $this->supplier,
                        'requestedQuantity' => $this->requestedQuantity,
                    ]);
    }
}