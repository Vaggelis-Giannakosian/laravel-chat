<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $receiver;
    /**
     * @var User
     */
    private User $sender;

    /**
     * Create a new event instance.
     *
     * @param User $receiver
     * @param User $sender
     * @param $message
     */
    public function __construct(User $receiver, User $sender, $message)
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('message.user.'. $this->receiver->id);
    }

    public function broadcastAs()
    {
        return 'NewMessage';
    }

    public function broadcastWith()
    {
        return [
            'sender'=>$this->sender,
            'message'=>$this->message
        ];
    }

//    public function tags(){
//        return ['tags'];
//    }
}
