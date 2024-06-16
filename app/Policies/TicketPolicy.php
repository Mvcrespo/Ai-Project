<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->purchase->customer_id || $user->type === 'A';
    }

    public function download(User $user, Ticket $ticket): bool
    {
        return ($user->id === $ticket->purchase->customer_id && $ticket->status === 'valid') || $user->type === 'A';
    }

    public function validateByQrCode(User $user): bool
    {
        return $user->type === 'E';
    }
}
