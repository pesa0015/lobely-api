<?php

namespace Tests\Feature;

use Tests\TestCase;

class MessagesTest extends TestCase
{
    /**
     * @group indexMessages
     *
     */
    public function testGetMessages()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $heart = factory('App\Heart')->create();

        factory('App\Message', 3)->create([
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
        ]);

        $user = factory('App\User')->create();

        factory('App\Message', 3)->create([
            'heart_id' => $heart->id,
            'user_id'  => $user->id,
        ]);

        $this->callHttpWithToken('GET', 'messages', $token, ['heartId' => $heart->id])
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id', 'body', 'createdAt', 'updatedAt'
                ]
            ]);
    }

    /**
     * @group storeMessage
     *
     */
    public function testStoreMessage()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $heart = factory('App\Heart')->create(['heart_user_id' => $me->user->id]);

        $payload = [
            'heartId' => $heart->id,
            'body'    => 'test',
        ];

        $this->callHttpWithToken('POST', 'messages', $token, $payload)
            ->assertStatus(200)
            ->assertJson([
                'body' => $payload['body'],
            ]);

        $this->assertDatabaseHas('messages', [
            'heart_id' => $payload['heartId'],
            'user_id'  => $me->user->id,
            'body'     => $payload['body'],
        ]);
    }

    /**
     * @group updateMessage
     *
     */
    public function testUpdateMessage()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $heart = factory('App\Heart')->create(['heart_user_id' => $me->user->id]);

        $message = factory('App\Message')->create([
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
        ]);

        $payload = [
            'body' => 'test',
        ];

        $this->callHttpWithToken('PUT', 'messages/' . $message->id, $token, $payload)
            ->assertStatus(200)
            ->assertJson([
                'id'        => $message->id,
                'body'      => $payload['body'],
                'createdAt' => $message->created_at,
                'updatedAt' => $message->updated_at,
            ]);

        $this->assertDatabaseMissing('messages', [
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
            'body'     => $message->body,
        ]);

        $this->assertDatabaseHas('messages', [
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
            'body'     => $payload['body'],
        ]);
    }

    /**
     * @group deleteMessage
     *
     */
    public function testDeleteMessage()
    {
        $me = $this->newUser(true);

        $token = $me->token;

        $heart = factory('App\Heart')->create(['heart_user_id' => $me->user->id]);

        $message = factory('App\Message')->create([
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
        ]);

        $this->callHttpWithToken('DELETE', 'messages/' . $message->id, $token)
            ->assertStatus(200);

        $this->assertDatabaseMissing('messages', [
            'heart_id' => $heart->id,
            'user_id'  => $me->user->id,
            'body'     => $message->body,
        ]);
    }
}
