<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Exceptions\AccessDeniedException;
use App\Models\AuthUser;
use App\Models\ChatChat;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
//
        Gate::define('chat-owner', function ($user, ChatChat $chat) {
            return ($chat->replyUser->id !== $user->id && $chat->ticketUser->id !== $user->id) ? Response::allow()
            : Response::noContent();
        });
    }
}
