<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\User;
use ReflectionClass;

class ServiceControllerTest extends TestCase
{
    private $http;
    private $existingCommands;
    private $nonExistingCommand;
    private $emailEmpoweredUser;
    private $emailNonEmpoweredUser;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $class = new ReflectionClass('\Controllers\ServiceController');
        $this->existingCommands = collect($class->getMethods(\ReflectionMethod::IS_PRIVATE))->map(fn($item) => $item->getName())->toArray();
        $this->nonExistingCommand = $this->existingCommands[0] . '1';
        $this->emailEmpoweredUser = 'test22@test.com';
        $this->emailNonEmpoweredUser = 'test22@test.com1';
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->existingCommands = NULL;
        $this->nonExistingCommand = NULL;
        $this->emailEmpoweredUser = NULL;
        $this->emailNonEmpoweredUser = NULL;
    }

    public function testServiceWithNonExistingCommand(): void
    {
        $user = new User();
        $token = $user->getToken($this->emailEmpoweredUser);
        $response = $this->http->request(
            'POST',
            '/api/service/' . $this->nonExistingCommand,
            [
                'form_params' => [
                    'email' => $this->emailEmpoweredUser,
                    'token' => $token
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => "Command $this->nonExistingCommand not found"]]));
    }

    public function testServiceWithExistingCommand(): void
    {
        $user = new User();
        $token = $user->getToken($this->emailEmpoweredUser);
        $randomCommand = $this->existingCommands[rand(0, count($this->existingCommands) - 1)];
        $response = $this->http->request(
            'POST',
            '/api/service/' . $randomCommand,
            [
                'form_params' => [
                    'email' => $this->emailEmpoweredUser,
                    'token' => $token
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => [$randomCommand => true]]));
    }

    public function testServiceWithEmailNonEmpoweredUser(): void
    {
        $user = new User();
        $token = $user->getToken($this->emailNonEmpoweredUser);
        $randomCommand = $this->existingCommands[rand(0, count($this->existingCommands) - 1)];
        $response = $this->http->request(
            'POST',
            '/api/service/' . $randomCommand,
            [
                'form_params' => [
                    'email' => $this->emailNonEmpoweredUser,
                    'token' => $token
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => ['no rights']]]));
    }
}
