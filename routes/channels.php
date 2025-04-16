<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

// Authorize the private channel for the chat
Broadcast::channel('chat.{receiver_id}', function ($user, $receiver_id) {
    // Allow access if the authenticated user is either the sender or receiver
    return (int) $user->id === (int) $receiver_id || Auth::id() === (int) $receiver_id;
});