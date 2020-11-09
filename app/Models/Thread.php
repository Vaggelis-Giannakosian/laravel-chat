<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Cmgmyr\Messenger\Models\Thread as BaseThread;
use Illuminate\Database\Eloquent\Model;

class Thread extends BaseThread
{
    use HasFactory;


    public function addMessage($body, $sender = null)
    {
        $sender = $sender ?? auth()->id();

        return $this->messages()->save(Message::factory()->create([
            'user_id'=>$sender,
            'body' => $body
        ]));
    }


}
