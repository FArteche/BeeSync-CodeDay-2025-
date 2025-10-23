<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Apiario;
use Illuminate\Support\Facades\URL;

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
        Gate::define('can-access-apiary', function (User $user, Apiario $apiario) {
            // 1. O usuário é o dono?
            if ($user->id === $apiario->user_id) { //
                return true;
            }

            // 2. Se não for o dono, ele é um membro convidado?
            return $apiario->membros()->where('user_id', $user->id)->exists();
        });

        /**
         * Gate: O usuário é o DONO do apiário?
         * (Apenas o Dono pode editar, excluir, convidar)
         */
        Gate::define('is-owner-of-apiary', function (User $user, Apiario $apiario) {
            return $user->id === $apiario->user_id; //
        });

        if (str_starts_with(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }
    }
}
