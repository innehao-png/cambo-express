<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Notifications\ResetPassword;
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
    public function boot(): void
    {
        // if (app()->environment('local')) {
        //     Http::globalOptions([
        //         'verify' => false,
        //     ]);
        // }

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url('/reset-password?token='.$token.'&email='.urlencode($user->email));
        });
    }
}
