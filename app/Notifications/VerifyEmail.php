<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends VerifyEmailNotification
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl( $notifiable )
    {
        return url( 'verify?' ) . URL::temporarySignedRouteQuery(
            'verification.verify', # Route URL : /api/verify
            Carbon::now()->addMinutes( config( 'auth.verification.expire', 60 ) ),
            ['id' => $notifiable->getKey()]
        );
    }
}