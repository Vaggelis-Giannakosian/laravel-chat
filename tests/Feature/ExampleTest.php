<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        Event::fake();


        $response = $this->get('/');

        Event::assertDispatched(MessageSent::class);

        $response->assertStatus(200);
    }
}
