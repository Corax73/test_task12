<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\User;

class AuthControllerTest extends TestCase
{
    private $http;
    private $invalidCredentials;
    private $validCredentials;
    private $invalidRegData;
    private $validRegData;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->invalidCredentials = [
            'form_params' => [
                'email' => 'phpunit@mail.php',
                'password' => 'test'
            ]
        ];
        $this->validCredentials = [
            'form_params' => [
                'email' => 'test1@test.com',
                'password' => '12345678'
            ]
        ];
        $this->invalidRegData = [
            'form_params' => [
                'email' => 'test1@test.com',
                'password' => '12345678',
                'password_confirm' => '12345678'
            ]
        ];
        $this->validRegData = [
            'form_params' => [
                'email' => 'phpunit@mail.php',
                'password' => '12345678',
                'password_confirm' => '12345678'
            ]
        ];
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->invalidCredentials = NULL;
        $this->invalidCredentials = NULL;
        $this->invalidRegData = NULL;
        $this->invalidRegData = NULL;
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/login',
            $this->invalidCredentials
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['errors' => ['invalid credentials']]));
    }

    public function testLoginWithValidCredentials(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/login',
            $this->validCredentials
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['token' => ""]));
    }

    public function testRegistrationWithInvalidRegData(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/registration',
            $this->invalidRegData
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['errors' => ['Email not unique']]));
    }

    public function testRegistrationWithValidRegData(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/registration',
            $this->validRegData
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => 'User created.']));
        $user = new User();
        $user_id = $user->getByEmail($this->validRegData['form_params']['email'])[0];
        $user->delete($user_id);
    }
}
