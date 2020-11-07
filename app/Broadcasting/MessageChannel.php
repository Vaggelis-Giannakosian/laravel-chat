<?php

namespace App\Broadcasting;

use App\Models\User;

class MessageChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param \App\Models\User $user
     * @param User $userClient
     * @return bool
     */
    public function join(User $user, User $userClient)
    {
        return $user->id === $userClient->id;
    }
}
