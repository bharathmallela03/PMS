<?php

namespace App\Mail;

use App\Models\Medicine;
use App\Models\Pharmacist;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $medicine;
    public $pharmacist;
    public $supplier;
    public $quantity;

    /**
     * Create a new message instance.
     * The quantity is now optional with a default of null.
     *
     * @return void
     */
    public function __construct(Medicine $medicine, Pharmacist $pharmacist, Supplier $supplier, $quantity = null)
    {
        $this->medicine = $medicine;
        $this->pharmacist = $pharmacist;
        $this->supplier = $supplier;
        $this->quantity = $quantity;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->quantity ? 'New Stock Request Received' : 'Low Stock Alert';

        return $this->subject($subject)
                     ->view('emails.stock-alert');
    }
}