<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserRoleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private array $oldRoles,
        private array $newRoles,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'user_role_changed',
            'title' => 'تم تغيير دور الحساب',
            'message' => 'تم تغيير دور حسابك في النظام.',
            'old_roles' => $this->oldRoles,
            'new_roles' => $this->newRoles,
        ];
    }
}
