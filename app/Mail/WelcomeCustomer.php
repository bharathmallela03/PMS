<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeCustomer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The customer instance.
     *
     * @var \App\Models\Customer
     */
    public $customer;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * This method is used in older Laravel versions to define the email's subject, view, and other properties.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Our Pharmacy!')
                    ->view('emails.customer.welcome');
    }
}
