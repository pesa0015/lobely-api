<?php

namespace Tests;

use App\User;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        config(['database.default' => 'testing']);

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        \Artisan::call('migrate');
    }

    /**
     * Helper function for creating users
     *
     */
    public function newUser($token = false, $returnPassword = false, $admin = false)
    {
        $passwordRaw = null;
        $JWTToken = null;
        
        if ($admin)
            $user = factory(User::class)->states('admin')->create();
        else
            $user = factory(User::class)->create();

        if ($token)
            $JWTToken = \JWTAuth::fromUser($user);
        if ($returnPassword)
            $passwordRaw = 'secret';
        // \JWTAuth::setToken($JWTToken);
        return (object) ['user' => $user, 'token' => $JWTToken, 'password' => $passwordRaw];
    }

    protected function callHttp($method, $url, $param = [])
    {
        $server = [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ];
        $response = $this->call($method, $url, $param, [], [], $server);
        
        return $response;
    }

    public function callHttpWithToken($method, $url, $token, $param = [])
    {
        $server = [
            'HTTP_Authorization'    => 'Bearer ' . $token,
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ];
        $response = $this->call($method, $url, $param, [], [], $server);
        
        // $token = str_replace('Bearer ', '', $response->headers->get('authorization'));
        return $response;
    }
}
