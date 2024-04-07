<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;
use Models\User;
use Models\UserMembership;
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
        $user = new User();
        $this->validUserId = $user->all(1)[0]['id'];
        $this->invalidGroupId = 0;
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

    public function testCreateWithInvalidGroupId(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/users/membership/',
            [
                'form_params' => [
                    'user_id' => $this->validUserId,
                    'group_id' => $this->invalidGroupId
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => ['Group not found']]]));
    }

    public function testCreateWithInvalidAllId(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/users/membership/',
            [
                'form_params' => [
                    'user_id' => $this->invalidUserId,
                    'group_id' => $this->invalidGroupId
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => [
                    'errors' => [
                        'User not found',
                        'Group not found'
                    ]
                ]
            ]
        ));
    }

    public function testCreateWithValidAllIdAndAlreadyAvailable(): void
    {
        $userMembership = new UserMembership();
        $alreadyAvailableUserId = $userMembership->users($this->validGroupId)[0];
        $response = $this->http->request(
            'POST',
            '/api/users/membership/',
            [
                'form_params' => [
                    'user_id' => $alreadyAvailableUserId,
                    'group_id' => $this->validGroupId
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => [
                    'errors' => "User with ID $alreadyAvailableUserId already available"
                ]
            ]
        ));
    }

    public function testShowUsersGroupsWithInvalidUserId(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/users/membership/' . $this->invalidUserId
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => 'User group membership not found']
            ]
        ));
    }

    public function testShowUsersGroupsWithValidUserId(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/users/membership/' . $this->validUserId
        );
        $data = json_decode($response->getBody()->getContents(), true);
        $this->assertTrue(count($data) > 0);
    }
}