<?php

namespace Tests;

use App\User;

class UserTest extends TestCase
{
    /**
     * All tests in this class are using the api
     *
     *=========================================*/

    /**
     * @group loginExistingUserWithFacebook
     * Tests login with Facebook
     */
    public function testLoginAsExistingUserWithFacebook()
    {
        $user = $this->newUser(false, true);

        $response = $this->callHttp('POST', '/auth/facebook', [
            'facebook_id' => $user->user->facebook_id,
            'name' => $user->user->name,
            'email' => $user->user->email,
            'gender' => $user->user->gender,
            'password' => $user->password
        ]);

        $response->assertJsonStructure([
            'token'
        ]);
    }

    /**
     * @group loginNewUserWithFacebook
     * Tests login with Facebook
     */
    public function testLoginAsNewUserWithFacebook()
    {
        $user = $this->newUser(false, true);

        $response = $this->callHttp('POST', '/auth/facebook', [
            'facebook_id' => 2,
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'gender' => 'male',
            'password' => 'test'
        ]);

        $response->assertJsonStructure([
            'token'
        ]);
    }

    /**
     * @group login
     * Tests login
     */
    public function testLogin()
    {
        $user = $this->newUser(false, true);

        $response = $this->callHttp('POST', '/auth', [
            'email' => $user->user->email,
            'password' => $user->password
        ]);

        $response->assertJsonStructure([
            'token'
        ]);
    }

    /**
     * @group logout
     * Tests logout
     */
    public function testLogout()
    {
        $user = $this->newUser(false, true);

        $token = $this->call('POST', '/auth/facebook', [
            'facebook_id' => $user->user->facebook_id,
            'name' => $user->user->name,
            'email' => $user->user->email,
            'gender' => $user->user->gender,
            'password' => $user->password
        ])->getData()->token;

        $response = $this->callHttpWithToken('POST', '/logout', $token);

        $response->assertStatus(200);
    }

    /**
     * @group forgotPassword
     * Tests send token for reset password
     */
    public function testForgotPassword()
    {
        $user = factory(User::class)->create([
            'email' => 'peters945@hotmail.com'
        ]);

        \Mail::shouldReceive('send')->once()->andReturnUsing(function($view, $content) {
            $this->assertEquals('emails.forgot-password', $view);
        });

        $response = $this->callHttp('POST', '/forgot-password', [
            'email' => $user->email
        ]);

        $response->assertStatus(200);
    }

    /**
     * @group resetPassword
     * Tests reset password
     */
    public function testResetPassword()
    {
        $user = factory(User::class)->create([
            'email' => 'peters945@hotmail.com'
        ]);

        $response = $this->call('POST', '/forgot-password', [
            'email' => $user->email
        ]);

        \Mail::shouldReceive('send')->once()->andReturnUsing(function($view, $content) {
            $this->assertEquals('emails.reset-password', $view);
        });

        $token = \App\PasswordReset::where('user_id', $user->id)->first()->token;

        $response = $this->callHttp('POST', '/reset-password', [
            'token'    => $token,
            'password' => 'Test123'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @group updateProfile
     * Tests update profile
     */
    public function testUpdateProfile()
    {
        $user = $this->newUser(true);

        $payload = [
            'name'  => $user->user->name,
            'gender' => $user->user->gender,
            'interested_in_gender' => $user->user->interested_in_gender,
            'birth_date' => $user->user->birth_date,
            'email' => 'test@example.com',
            'bio'   => 'Some text about me'
        ];

        $this->assertDatabaseHas('users', [
            'id'    => $user->user->id,
            'email' => $user->user->email,
            'bio'   => $user->user->bio
        ]);

        $response = $this->callHttpWithToken('PATCH', '/user/profile/update/' . $user->user->id, $user->token, $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id'    => $user->user->id,
            'email' => $payload['email'],
            'bio'   => $payload['bio']
        ]);
    }

    /**
     * @group emailAlreadyUsed
     * Tests using email already used
     */
    public function testEmailAlreadyUsed()
    {
        $user = factory(User::class)->create([
            'email' => 'peters945@hotmail.com'
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => $user->email
        ]);

        $newUserWithSameEmail = [
            'name'     => 'Peter Sall',
            'email'    => 'peters945@hotmail.com',
            'gender'   => 'male',
            'password' => 'Test123'
        ];

        $response = $this->callHttp('POST', '/register', $newUserWithSameEmail);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'email' => [
                0 => 'The email has already been taken.'
            ]
        ]);

        $newUserWithDifferentEmail = $newUserWithSameEmail;
        $newUserWithDifferentEmail['email'] = 'new@example.com';

        $response = $this->callHttp('POST', '/register', $newUserWithDifferentEmail);

        $response->assertStatus(200);
        $userId = $response->getData()->id;

        $updateUser = $newUserWithSameEmail;
        $token = \JWTAuth::fromUser(User::findOrFail($userId));

        $response = $this->callHttpWithToken('PATCH', '/user/profile/update/' . $userId, $token, $updateUser);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'email' => [
                0 => 'The email has already been taken.'
            ]
        ]);
    }
}
