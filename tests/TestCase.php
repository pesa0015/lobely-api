<?php

namespace Tests;

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

    public function callHttpWithToken($method, $url, $token, $param = [])
    {
        $server = [
            'HTTP_Authorization' => 'Bearer ' . $token
        ];
        $response = $this->call($method, $url, $param, [], [], $server);
        
        // $token = str_replace('Bearer ', '', $response->headers->get('authorization'));
        return $response;
    }
}
