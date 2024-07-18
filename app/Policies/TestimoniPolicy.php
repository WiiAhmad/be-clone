<?php

namespace App\Policies;

use App\Models\User;
use App\Models\testimoni;
use Illuminate\Auth\Access\Response;

class TestimoniPolicy
{
    public function modify(User $user, testimoni $testimoni): Response
    {
        return $user->id === $testimoni->user_id
            ? Response::allow()
            : Response::deny('You do not own this testimoni.');
    }
}
