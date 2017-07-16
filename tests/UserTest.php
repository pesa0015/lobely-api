<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * All tests in this class are using the api
     *
     *=========================================*/

    /**
     * Helper function for creating users
     *
     */
    public function newUser($token = false, $returnPassword = false, $admin = false)
    {
        $passwordRaw = null;
        $JWTToken = null;
        
        if ($admin)
            $user = factory(App\User::class)->states('admin')->create();
        else
            $user = factory(App\User::class)->create();

        if ($token)
            $JWTToken = \JWTAuth::fromUser($user);
        if ($returnPassword)
            $passwordRaw = 'secret';
        // \JWTAuth::setToken($JWTToken);
        return (object) ['user' => $user, 'token' => $JWTToken, 'password' => $passwordRaw];
    }

    /**
     * @group loginExistingUserWithFacebook
     * Tests login with Facebook
     */
    public function testLoginAsExistingUserWithFacebook()
    {
        $user = $this->newUser(false, true);

        $this->call('POST', '/api/auth/facebook', [
            'facebook_id' => $user->user->facebook_id,
            'name' => $user->user->name,
            'email' => $user->user->email,
            'gender' => $user->user->gender,
            'password' => $user->password
        ]);

        $this->seeJsonStructure([
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

        $this->call('POST', '/api/auth/facebook', [
            'facebook_id' => 2,
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'gender' => 'male',
            'password' => 'test'
        ]);

        $this->seeJsonStructure([
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

        $this->call('POST', '/api/auth', [
            'email' => $user->user->email,
            'password' => $user->password
        ]);

        $this->seeJsonStructure([
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

        $token = $this->call('POST', '/api/auth/facebook', [
            'facebook_id' => $user->user->facebook_id,
            'name' => $user->user->name,
            'email' => $user->user->email,
            'gender' => $user->user->gender,
            'password' => $user->password
        ])->getData()->token;

        $this->callHttpWithToken('POST', '/api/logout', $token);

        $this->assertResponseOk();
    }

    /**
     * @group forgotPassword
     * Tests send token for reset password
     */
    public function testForgotPassword()
    {
        $user = factory(App\User::class)->create([
            'email' => 'peters945@hotmail.com'
        ]);

        \Mail::shouldReceive('send')->once()->andReturnUsing(function($view, $content) {
            $this->assertEquals('emails.forgot-password', $view);
        });

        $response = $this->call('POST', '/api/forgot-password', [
            'email' => $user->email
        ]);

        $this->assertResponseOk();
    }
}