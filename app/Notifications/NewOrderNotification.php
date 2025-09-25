<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $nobukti, public $noKamar, public $toUser, public $fromUser, public $transType, public $message)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    function broadcastType(): string
    {
        return 'broadcast.message';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    function toDatabase()
    {
        return [
            'nobukti' =>  $this->nobukti,
            'noKamar'   =>  $this->noKamar,
            'toUser'   =>  $this->toUser,
            'fromUser'   =>  $this->fromUser,
            'message'   =>  $this->message,
            'transType'   =>  $this->transType,
        ];
    }

    function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'nobukti' =>  $this->nobukti,
            'noKamar'   =>  $this->noKamar,
            'toUser'   =>  $this->toUser,
            'fromUser'   =>  $this->fromUser,
            'message'   =>  $this->message,
            'transType'   =>  $this->transType,
        ]);
    }
}
