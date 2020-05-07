<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $expire;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, string $expire)
    {
        $this->token = $token;
        $this->expire = $expire;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('mails.reset_password'))
            ->view('mails.reset_password');
    }
}
