<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $userType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $token, $userType)
    {
        $this->user = $user;
        $this->token = $token;
        $this->userType = $userType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password-setup')
                    ->subject('Setup Your Password - Pharmacy Management System')
                    ->with([
                        'user' => $this->user,
                        'setupUrl' => route('password.setup', $this->token),
                        'userType' => ucfirst($this->userType),
                    ]);
    }
}