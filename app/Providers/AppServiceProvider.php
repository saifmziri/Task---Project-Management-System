<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // تعريف حماية خاصة بالـ Login
        RateLimiter::for('login', function (Request $request) {
        // نحدد المحاولات بناءً على الإيميل المدخل + الـ IP
        return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip())->response(function () {
            return response()->json([
                'message' => 'Too many login attempts. Please try again in 1 minute.'
            ], 429);
        });
    });
    }
}
