<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // some part of the website are only allowed to superadmin
        Gate::define('superadmin', function (User $user) {
            return $user->isSuperAdmin();
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage())
                ->view('emails.verification', ['url' => $url])
            ;
        });
    }
}
