<?php

namespace App\Providers;

use App\Models\DeclarationOfOriginality;
use App\Models\InternshipClearance;
use App\Policies\DeclarationOfOriginalityPolicy;
use App\Policies\InternshipClearancePolicy;
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
        Gate::policy(DeclarationOfOriginality::class, DeclarationOfOriginalityPolicy::class);
        Gate::policy(InternshipClearance::class, InternshipClearancePolicy::class);
    }
}

