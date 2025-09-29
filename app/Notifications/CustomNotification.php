<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;

    /**
     * Cria nova notificação customizada.
     */
    public function __construct($title, $message)
    {
        $this->title   = $title;
        $this->message = $message;
    }

    /**
     * Quais canais de entrega? Usar database para exibir no sistema.
     */
    public function via($notifiable)
    {
        // Se quiser email + banco, use ['database','mail'];
        // Só banco de dados (recomendado para Bell Icon):
        return ['database'];
    }

    /**
     * O que será salvo no banco (usado pelo menu de notificações).
     */
    public function toDatabase($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
        ];
    }

    /**
     * (Opcional) Como seria o email, caso queira testar.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->title)
            ->line($this->message);
    }
}