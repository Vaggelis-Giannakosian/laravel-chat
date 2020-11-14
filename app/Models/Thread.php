<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Cmgmyr\Messenger\Models\Thread as BaseThread;
use Illuminate\Database\Eloquent\Model;

class Thread extends BaseThread
{
    use HasFactory;

    protected $appends = ['unreadMessages'];

    public function addMessage($body, $sender = null)
    {
        $sender = $sender ?? auth()->id();

        return $this->messages()->save(Message::factory()->create([
            'user_id'=>$sender,
            'body' => $body
        ]));
    }

    public function getunreadMessagesAttribute(){
        return $this->userUnreadMessagesCount(auth()->id());
    }

    public function lastMessage(){
        return $this->hasOne(Message::class)->latest();
    }

    public function updateLastRead($userId = null)
    {
        $userId = $userId ?: auth()->id();

        $participant = $this->participants->where('user_id',$userId)->first();
        $participant->last_read = now();
        $participant->save();
    }

}
