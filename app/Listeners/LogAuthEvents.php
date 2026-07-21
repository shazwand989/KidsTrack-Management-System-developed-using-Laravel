<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;

class LogAuthEvents
{
    public function handleLogin(Login $event): void
    {
        AuditService::log('login', 'Auth', null, null, null,
            $event->user->name . ' logged in');
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            AuditService::log('logout', 'Auth', null, null, null,
                $event->user->name . ' logged out');
        }
    }

    public function handleRegistered(Registered $event): void
    {
        $user = $event->user;
        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            AuditService::log('registered', 'Auth', $user);
        }
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $user = $event->user;
        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            AuditService::log('password_reset', 'Auth', $user);
        }
    }
}
