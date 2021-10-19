<?php

namespace Techkken\Cashier\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CashierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../Http/routes.php';

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'techkken');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'techkken');

        Event::listen('techkken', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('techkken::cashier.layouts.style');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
       );
    }
}
