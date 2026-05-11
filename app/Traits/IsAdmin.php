<?php

namespace App\Traits;

use Illuminate\Auth\AuthenticationException;

trait IsAdmin
{
    /**
     * Check if the authenticated user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->role?->name === 'admin';
    }

    /**
     * Authorize that the authenticated user is an admin
     *
     * @return void
     * @throws AuthenticationException
     */
    public function authorizeAdmin(): void
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Check if a user is admin
     *
     * @param $user
     * @return bool
     */
    public function userIsAdmin($user): bool
    {
        return $user && $user->role?->name === 'admin';
    }
}
