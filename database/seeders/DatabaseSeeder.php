<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        if($this->command->confirm('Do you want to refresh the database?',true))
        {
            $this->command->call('migrate:fresh');
            $this->command->info('Database was refreshed');
        }

         $lostre = User::factory()->lostre()->create();
         $loli = User::factory()->loli()->create();

         $users = User::factory(10)->create();

         $threads = Thread::factory(10)->create();

         $users->each(function($user) use ($threads,$lostre,$loli){

             $thread = $threads->random();

             $thread->addParticipant($user->id);
             $thread->addParticipant($lostre->id);
             $thread->addParticipant($loli->id);

             Message::factory(2)->create([
                 'user_id'=>$user,
                 'thread_id' => $thread
             ]);


         });
    }
}
