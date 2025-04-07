<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Illuminate\Support\Facades\Log;

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('group.chat.{groupId}', function ($user, $groupId) {
    $isMember = Group::where('id', $groupId)
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
    return $isMember;
});

Broadcast::channel('notifications.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});


Broadcast::channel('user-status.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});