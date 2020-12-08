<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Cmgmyr\Messenger\Models\Message as BaseMessage;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Message extends BaseMessage
{
    use HasFactory,Searchable;



    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        unset($array['updated_at']);

        return $array;
    }

}



