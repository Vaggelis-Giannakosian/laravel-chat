<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Cmgmyr\Messenger\Models\Message as BaseMessage;
use Illuminate\Database\Eloquent\Model;

class Message extends BaseMessage
{
    use HasFactory;
}
