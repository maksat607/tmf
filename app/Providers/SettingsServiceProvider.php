<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/settings.php', 'settings'
        );
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });
    }

    public function boot()
    {
        $this->app['config']['cache.stores.settings'] = [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ];

        $fallbackSettings = Config::get('settings.fallbacks');
        foreach ($fallbackSettings as $key => $value) {
            $cachedValue = cache()->store('settings')->get('setting_' . $key);
            if (!$cachedValue) {
                cache()->store('settings')->put('setting_' . $key, $value);
            }
        }
    }
}
