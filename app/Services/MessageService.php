<?php

namespace App\Services;

use App\Models\User;

class MessageService
{
    public function getHistory(User $user)
    {
        return $user->messages()->orderBy('id', 'desc')->paginate(20);
    }
}
