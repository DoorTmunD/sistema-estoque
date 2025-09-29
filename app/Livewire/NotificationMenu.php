<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationMenu extends Component
{
    public function render()
    {
        $user = auth()->user();
        $notifications = $user
            ? $user->unreadNotifications()->latest()->take(10)->get()
            : collect();

        return view('livewire.notification-menu', compact('notifications'));
    }

    public function markAsRead(string $notificationId): void
    {
        $user = auth()->user();
        if (!$user) return;

        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();
        if (!$user) return;

        $user->unreadNotifications->markAsRead();
    }
}
