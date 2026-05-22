<?php

namespace App\Providers;

use App\Models\Investim;
use App\Models\Kompania;
use App\Models\RaundiFinancimit;
use App\Models\User;
use App\Policies\InvestimPolicy;
use App\Policies\KompaniaPolicy;
use App\Policies\RaundiFinancimitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();
        Gate::policy(Kompania::class, KompaniaPolicy::class);
        Gate::policy(RaundiFinancimit::class, RaundiFinancimitPolicy::class);
        Gate::policy(Investim::class, InvestimPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

         Relation::morphMap([
        'kompania' => Kompania::class,
    ]);
    }
}
