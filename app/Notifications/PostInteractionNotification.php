<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostInteractionNotification extends Notification
{
    use Queueable;

    protected $interactionType; // 'like' or 'comment'
    protected $sender;
    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($interactionType, $sender, $post)
    {
        $this->interactionType = $interactionType;
        $this->sender = $sender;
        $this->post = $post;
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->interactionType, // 'like' or 'comment'
            'message' => match ($this->interactionType) {
                'like' => __(":name liked your post.", ['name' => $this->sender->name]),
                'comment' => __(":name commented on your post.", ['name' => $this->sender->name]),
                'comment_reply' => __(":name also commented on :owner_name's post.", [
                    'name' => $this->sender->name,
                    'owner_name' => $this->post->user->name
                ]),
                default => __("New interaction on a post.")
            },
            'sender_name' => $this->sender->name,
            'sender_avatar' => $this->sender->profile_photo_url, // Assuming standard Laravel accessor
            'post_id' => $this->post->id,
            'link' => route('feed.show', $this->post->id), // Ensure route exists or feed anchor
        ];
    }
}
