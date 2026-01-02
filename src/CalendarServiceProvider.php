<?php

namespace LabaPawel\Calendar;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class CalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'calendar');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'calendar');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/calendar'),
            ], 'calendar-views');
        }
    }
}
