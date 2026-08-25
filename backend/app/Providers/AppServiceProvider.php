<?php

namespace App\Providers;

use App\Models\Appuntamento;
use App\Models\Referto;
use App\Policies\AppuntamentoPolicy;
use App\Policies\RefertoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Appuntamento::class, AppuntamentoPolicy::class);
        Gate::policy(Referto::class, RefertoPolicy::class);
    }
}
