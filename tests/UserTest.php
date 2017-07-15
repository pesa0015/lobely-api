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
     * @group loginExistingUser
     * Tests login
     */
    public function testLoginAsExistingUser()
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
     * @group loginNewUser
     * Tests login
     */
    public function testLoginAsNewUser()
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
}