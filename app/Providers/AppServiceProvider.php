<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Please verify Email Address')
                ->line('Thank you for signing up on ' . config('app.name') . '. Please click the button below to verify your email address and activate your account.')
                ->action('Verify Email Address', $url);
        });
    }
}
