<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use URL;

class TeamInvitation extends Notification
{
    use Queueable;
    protected $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invitation)
    {
        //
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $acceptUrl = route('team-invitations.accept', [
            'invitation' => $this->invitation,
        ]);
        return [
            'acceptUrl'=> $acceptUrl,
            'id'=> $this->invitation->id,
            'message' => [
                'title' => __('Team Invitation'),
                'content' => __('You have been invited to join the :team team!', ['team' => $this->invitation->team->name]),
                'footer' => __('If you did not expect this, you may ignore this notification.'),
            ],
            'button' => [
                'title' => __('Yo may accept this invitation by clicking below'),
                'link' => $acceptUrl,
                'text' =>  __('Accept Invitation'),
            ],
        ];
    }
    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
