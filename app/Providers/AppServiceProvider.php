<?php

namespace App\Providers;

use App\Services\TicketService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Services\PushService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TicketService::class, function ($app) {
            return new TicketService();
        });
        $this->app->singleton('telegram', function ($app) {
            return new \App\Services\TelegramService();
        });
        $this->app->singleton(PushService::class, function ($app) {
            $oneSignalBaseUrl = config('onesignal.base_url');
            $oneSignalAppId = config('onesignal.app_id');
            $oneSignalApiKey = config('onesignal.api_key');

            return new PushService($oneSignalBaseUrl, $oneSignalAppId, $oneSignalApiKey);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
