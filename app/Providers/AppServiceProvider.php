<?php

namespace App\Providers;

use App\Filament\App\Pages\ClinicProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use URL;

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
        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();

        // Ensure the page alias exists even if production discovery/cache is stale.
        Livewire::component('app.filament.app.pages.clinic-profile', ClinicProfile::class);

        if ($this->app->environment() === 'production') {
            Url::forceScheme('https');
        }
    }
}
