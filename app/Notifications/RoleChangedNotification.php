<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $action;
    private string $roleName;
    private ?string $actorName;

    public function __construct(string $action, string $roleName, ?string $actorName)
    {
        $this->action = $action;
        $this->roleName = $roleName;
        $this->actorName = $actorName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $action = ($this->action === 'assigned') ? 'assigned to' : 'removed from';
        $role = $this->roleName;
        $actor = $this->actorName ?? 'System';
        return (new MailMessage)
            ->subject('[GlobMall] Your account role has been changed')
            ->line("Hello {$notifiable->name},")
            ->line("You have been {$action} the role \"{$role}\".")
            ->line('Actor: ' . $actor)
            ->action('View your account', url('/account'))
            ->line('If you did not expect this change, please contact the administrator.');
    }

    public function toDatabase(object $notifiable): array
    {
        $msg = ($this->action === 'assigned')
            ? "You were assigned the role \"{$this->roleName}\"."
            : "The role \"{$this->roleName}\" was removed from you.";
        return [
            'action'     => $this->action,
            'role_name'  => $this->roleName,
            'actor_name' => $this->actorName,
            'message'    => $msg,
        ];
    }
}
