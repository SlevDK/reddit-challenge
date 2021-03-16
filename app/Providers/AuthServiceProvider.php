<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-board', static function (User $user, Board $board) {
            return $board->isModerator($user);
        });

        Gate::define('destroy-board', static function (User $user, Board $board) {
            return $board->isModerator($user);
        });

        Gate::define('manage-board', static function (User $user, Board $board) {
            return $board->isModerator($user);
        });

        Gate::define('create-thread', static function (User $user, Board $board) {
            $role = $board->getUserRole($user);

            if ($role === Board::ROLE_MODERATOR) {
                return true;
            }

            if ($role == Board::ROLE_MEMBER) {
                $opened_thread_count = $board->openedThreadsCount($user);
                // todo: count into the config or smth?
                return $opened_thread_count < 1;
            }

            return false;
        });

        Gate::define('update-thread', static function (User $user, Board $board) {
            $role = $board->getUserRole($user);

            return $role === Board::ROLE_MODERATOR;
        });

        Gate::define('create-comment', static function (User $user, Thread $thread) {
            $board = $thread->board;

            return $thread->isOpened() && ! $board->isBanned($user);
        });
    }
}
