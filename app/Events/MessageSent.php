<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
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
    public $thread;
    public $sender;


    public function __construct(User $sender, Thread $thread, Message $message)
    {
        $this->sender = $sender->only(['name','email','id']);
        $this->thread = $thread;
        $this->message = $message;

        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("thread.{$this->thread->id}");
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
