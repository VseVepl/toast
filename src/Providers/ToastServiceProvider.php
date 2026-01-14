<?php

namespace Vsent\LaravelLivewireToasts\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\LivewireManager; // Or use Livewire\Livewire; for older versions
use Vsent\LaravelLivewireToasts\Http\Livewire\Toast as LivewireToastComponent; // Alias the component
use Vsent\LaravelLivewireToasts\Helpers\ToastMessage;

class ToastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/toasts.php', 'toasts'
        );

        // Bind the ToastMessage helper class to the service container
        $this->app->singleton(ToastMessage::class, function ($app) {
            return new ToastMessage();
        });

        // For older Laravel versions or if not using discovery, you might need to register the alias manually:
        // $this->app->alias(ToastMessage::class, 'laravel-livewire-toasts.toast');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(LivewireManager $livewire) // Type-hint LivewireManager
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-livewire-toasts');

        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/laravel-livewire-toasts'),
        ], ['toasts-views', 'laravel-assets']); // Added group 'laravel-assets' for convenience

        // Load routes - typically not needed for a toast package unless it has a dashboard or specific endpoints
        // $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');

        // Register Livewire component
        $livewire->component('livewire-toast', \Vsent\LaravelLivewireToasts\Http\Livewire\Toast::class); // Use the aliased Livewire component

        // Publish config
        $this->publishes([
            __DIR__.'/../../config/toasts.php' => config_path('toasts.php'),
        ], ['toasts-config', 'laravel-assets']);

        // Publish assets
        $this->publishes([
            __DIR__.'/../../public' => public_path('vendor/laravel-livewire-toasts'),
        ], ['toasts-assets', 'laravel-assets']);

        // You can define more specific publish groups if needed, for example:
        // $this->publishes([__DIR__.'/../../config/toasts.php' => config_path('toasts.php')], 'toasts-config');
        // $this->publishes([__DIR__.'/../../resources/views' => resource_path('views/vendor/laravel-livewire-toasts')], 'toasts-views');
        // $this->publishes([__DIR__.'/../../public' => public_path('vendor/laravel-livewire-toasts')], 'toasts-assets');
    }
}
