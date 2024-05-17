<?php

namespace App\Providers;

use App\Http\Services\SearchQueryHandlerInterface;
use App\Http\Services\UserSearchQueryHandler;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        SearchQueryHandlerInterface::class => UserSearchQueryHandler::class,
    ];
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
        JsonResource::withoutWrapping();
        Model::shouldBeStrict();
        //RateLimiter::for('global', function (Request $request) {
        //    return Limit::perMinute(1);
        //});
        //RateLimiter::for('api', function (Request $request) {
        //    return Limit::perMinute(1)->by($request->user()?->id ?: $request->ip());
        //});   //
    }
}
