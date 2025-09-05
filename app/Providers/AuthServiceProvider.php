<?php

namespace App\Providers;
use App\Models\Transaction;
use App\Policies\TransactionPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
         Transaction::class => TransactionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Daftarkan Gate 'hasFeature' kita di sini.
        // Gate ini akan memanggil method hasFeature() yang ada di model User.
        Gate::define('hasFeature', function (User $user, string $feature) {
            return $user->hasFeature($feature);
        });
    }
}
