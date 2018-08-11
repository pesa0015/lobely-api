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
            'facebookId' => $user->user->facebook_id,
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
        $response = $this->callHttp('POST', '/auth/facebook', [
            'facebookId' => 2,
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'gender' => 'male',
            'password' => 'test'
        ]);

        $response->assertJsonStructure([
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'facebook_id' => 2,
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'gender' => 'm'
        ]);

        $response = $this->callHttp('POST', '/auth/facebook', [
            'facebookId' => 3,
            'name' => 'Test3',
            'email' => 'test3@gmail.com',
            'gender' => 'female',
            'password' => 'test'
        ]);

        $response->assertJsonStructure([
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'facebook_id' => 3,
            'name' => 'Test3',
            'email' => 'test3@gmail.com',
            'gender' => 'f'
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
            'facebookId' => $user->user->facebook_id,
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
     * @group showProfile
     * Tests show profile
     */
    public function testShowProfile()
    {
        $user = $this->newUser(true);

        $response = $this->callHttpWithToken('GET', '/user/profile', $user->token);

        $response->assertStatus(200);

        $response->assertJson([
            'id'                 => $user->user->id,
            'name'               => $user->user->name,
            'email'              => $user->user->email,
            'img'                => $user->user->profile_img,
            'interestedInGender' => $user->user->interested_in_gender,
            'birthDate'          => $user->user->birth_date,
            'bio'                => $user->user->bio
        ]);

        $response->assertJsonMissing([
            'facebookId'  => $user->user->facebook_id
        ]);
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
            'interestedInGender' => $user->user->interested_in_gender,
            'birthDate' => $user->user->birth_date,
            'email' => 'test@example.com',
            'bio'   => 'Some text about me'
        ];

        $this->assertDatabaseHas('users', [
            'id'    => $user->user->id,
            'email' => $user->user->email,
            'bio'   => $user->user->bio
        ]);

        $response = $this->callHttpWithToken('PUT', '/user/profile', $user->token, $payload);

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
            'password' => 'Test123',
            'birthDate' => '10/1/1978',
            'interestedInGender' => 'f'
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

        $response = $this->callHttpWithToken('PUT', '/user/profile', $token, $updateUser);

        $response->assertStatus(422);

        $response->assertJsonFragment([
            'email' => [
                0 => 'The email has already been taken.'
            ]
        ]);
    }

    /**
     * @group changePassword
     * Tests changing password
     */
    public function testChangePassword()
    {
        $user = $this->newUser(true, true);

        $payload = [
            'currentPassword'   => $user->password,
            'newPassword'       => 'test123',
            'repeatNewPassword' => 'test123'
        ];

        $this->assertDatabaseHas('users', [
            'password' => $user->user->password
        ]);

        $response = $this->callHttpWithToken('PUT', '/user/profile/password', $user->token, $payload);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'password' => $user->user->password
        ]);
    }

    /**
     * @group changePasswordFails
     * Tests changing password
     */
    public function testChangePasswordFailsWithWrongPassword()
    {
        $user = $this->newUser(true, true);

        $payload = [
            'currentPassword'   => 'current',
            'newPassword'       => 'test123',
            'repeatNewPassword' => 'test123'
        ];

        $this->assertDatabaseHas('users', [
            'password' => $user->user->password
        ]);

        $response = $this->callHttpWithToken('PUT', '/user/profile/password', $user->token, $payload);

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'password' => $user->user->password
        ]);
    }
}
