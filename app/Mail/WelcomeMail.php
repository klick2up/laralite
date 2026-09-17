<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class WelcomeMail extends Mailable
{
    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public User $user;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message using Blade view.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to Laralite!')
                    ->view('emails.welcome', [
                        'appName' => env('APP_NAME', 'Laralite'),
                        'user' => $this->user,
                    ]);
    }
}
