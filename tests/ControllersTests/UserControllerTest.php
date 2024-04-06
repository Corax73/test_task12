<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;
use Models\User;
use ReflectionClass;

class UserControllerTest extends TestCase
{
    private $http;
    private $invalidUserId;
    private $validUserId;
    private $invalidGroupId;
    private $validGroupId;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->invalidUserId = 0;
        $this->validUserId = '';
        $this->invalidGroupId = '';
        $group = new Group();
        $this->validGroupId = $group->all(1)[0]['id'];
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->invalidUserId = NULL;
        $this->validUserId = NULL;
        $this->invalidGroupId = NULL;
        $this->validGroupId = NULL;
    }

    public function testCreateWithInvalidUserId(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/users/membership/',
            [
                'form_params' => [
                    'user_id' => $this->invalidUserId,
                    'group_id' => $this->validGroupId
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => ['User not found']]]));
    }
}
