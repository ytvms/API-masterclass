<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Configuration\Exceptions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    function register(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );

        $using ??= fn () => true;

        $this->app->afterResolving(
            \Illuminate\Foundation\Exceptions\Handler::class,
            fn ($handler) => $using(new Exceptions($handler)),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
