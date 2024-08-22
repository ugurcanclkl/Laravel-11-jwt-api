<?php

namespace App\Providers;

use App\Exceptions\NotExistsException;
use App\Exceptions\UnauthorizedException;
use App\Models\Room;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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
        $this->configureRateLimiting();

        Route::bind('assignedRoom', function ($value): Room {
            $user = auth()->guard('api')->user();

            if (! $user) {
                throw new NotExistsException();
            }

            $room = $user->rooms()->find($value);

            if (! $room) {
                // @TODO: custom exception yazilmali ve dont report a eklenmeli kullaniciya json donmeli..
                throw new UnauthorizedException();
            }

            return $room;
        });

        Route::bind('ownedRoomUser', function ($value): Room {
            $user = auth()->guard('api')->user();

            if (! $user) {
                throw new NotExistsException();
            }

            $ownedRoomUser = $user->roomusers()->find($value);

            if (! $ownedRoomUser) {
                // @TODO: custom exception yazilmali ve dont report a eklenmeli kullaniciya json donmeli..
                throw new UnauthorizedException();
            }

            return $ownedRoomUser;
        });

        Route::bind('userMessage', function ($value): Room {
            $user = auth()->guard('api')->user();

            if (! $user) {
                throw new NotExistsException();
            }

            $userMessage = $user->messages()->find($value);

            if (! $userMessage) {
                // @TODO: custom exception yazilmali ve dont report a eklenmeli kullaniciya json donmeli..
                throw new UnauthorizedException();
            }

            return $userMessage;
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                        ? Limit::perMinute(1000)->by($request->user()->id)->response(fn (Request $request, array $headers) => response('Too Many Requests', 429, $headers))
                        : Limit::perMinute(1000)->by($request->ip())->response(fn (Request $request, array $headers) => response('Too Many Requests', 429, $headers));
        });
    }
}
