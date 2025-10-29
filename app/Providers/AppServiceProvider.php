<?php

namespace App\Providers;

use zahidhassanshaikot\Settings\Facades\Settings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\CustomApiExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandlerContract::class, CustomApiExceptionHandler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        $this->loadAndSetSettings();

        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

    }


    private function loadAndSetSettings(): void
    {
        try {
            $settings = $this->getCachedSettings();
            $this->setConfigSettings($settings);
            $this->setAppTimezone($settings);
        } catch (\Exception $exception) {
            $this->logException($exception);
        }
    }

    private function getCachedSettings(): array
    {
        return Cache::rememberForever('system_settings', function () {
            return Settings::all()->keyBy('key')
                ->transform(function ($setting) {
                    return $setting->value;
                })
                ->toArray();
        });
    }

    private function setConfigSettings(array $settings): void
    {
        Config::set('settings', $settings);
    }

    private function setAppTimezone(array $settings): void
    {
        if (isset($settings['timezone'])) {
            config([
                'app.timezone' => $settings['timezone']
            ]);
        }
    }

    private function logException(\Exception $exception): void
    {
        Log::debug('App service provider boot method config error: ' . $exception->getMessage());
    }
}
