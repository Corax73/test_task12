<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;
use Models\TempBlockedUsers;
use Models\User;
use Models\UserMembership;

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

    public function testShowUsersRightsWithInvalidUserId(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/users/rights/' . $this->invalidUserId
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => ['User not found']]
            ]
        ));
    }

    public function testShowUsersRightsWithValidUserId(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/users/rights/' . $this->validUserId
        );
        $user = new User();
        $rights = collect($user->getRights($this->validUserId))->collapse()->values()->toArray();
        $result = [];
        foreach (json_decode($response->getBody()->getContents(), true) as $key => $val) {
            if ($val && in_array($key, $rights)) {
                $result[] = $key;
            }
        }
        $this->assertTrue(!array_diff($result, $rights));
    }

    public function testDestroyUserMembershipWithInvalidUserId(): void
    {
        $response = $this->http->request(
            'DELETE',
            '/api/users/membership/' . $this->invalidUserId . '/' . $this->validGroupId
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => ['User not found']]
            ]
        ));
    }

    public function testDestroyUserMembershipWithInvalidGroupId(): void
    {
        $response = $this->http->request(
            'DELETE',
            '/api/users/membership/' . $this->validUserId . '/' . $this->invalidGroupId
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => ['Group not found']]
            ]
        ));
    }

    public function testDestroyUserMembershipWithValidData(): void
    {
        $response = $this->http->request(
            'DELETE',
            '/api/users/membership/' . $this->validUserId . '/' . $this->validGroupId
        );
        $userMembership = new UserMembership();
        $userMembership->save($this->validUserId, $this->validGroupId);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => 'User membership removed.'
            ]
        ));
    }

    public function testSetTempBlockedUsersWithInvalidUserId(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/users/temp-blocked/',
            [
                'form_params' => [
                    'user_id' => $this->invalidUserId
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => ['User not found']]
            ]
        ));
    }

    public function testSetTempBlockedUsersWithAlreadyBlockedUserId(): void
    {
        $tempBlocked = new TempBlockedUsers();
        $id = $tempBlocked->all(1);
        if($id) {
            $id = $id[0]['user_id'];
        } else {
            $tempBlocked->save($this->validUserId);
            $id = $this->validUserId;
        }
        $response = $this->http->request(
            'POST',
            '/api/users/temp-blocked/',
            [
                'form_params' => [
                    'user_id' => $id
                ]
            ]
        );
        $tempBlocked->delete($id);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(
            [
                'response' => ['errors' => ["User with ID $id already blocked"]]
            ]
        ));
    }
}
