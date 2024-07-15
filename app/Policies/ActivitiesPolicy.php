<?php

namespace App\Policies;

use App\Models\User;
use App\Models\activities;
use Illuminate\Auth\Access\Response;

class ActivitiesPolicy
{
    public function modify(User $user, activities $activities): Response
    {
        return $user->id === $activities->user_id
            ? Response::allow()
            : Response::deny('You do not own this activity.');
    }
}
