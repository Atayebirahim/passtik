<?php

namespace App\Policies;

use App\Models\Router;
use App\Models\User;

class RouterPolicy
{
    public function view(User $user, Router $router): bool
    {
        return $user->id === $router->user_id || $user->is_admin;
    }

    public function update(User $user, Router $router): bool
    {
        return $user->id === $router->user_id;
    }

    public function delete(User $user, Router $router): bool
    {
        return $user->id === $router->user_id;
    }
}
