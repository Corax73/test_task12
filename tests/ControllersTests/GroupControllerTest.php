<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use Controllers\GroupController;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;

class GroupControllerTest extends TestCase
{
    private $http;
    private $existingGroup;
    private $nonExistentGroup;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->existingGroup = 'unverified_users';
        $this->nonExistentGroup = 'users1';
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->existingGroup = NULL;
        $this->nonExistentGroup = NULL;
    }

    public function testCreateGroupController(): void
    {
        $this->assertContainsOnlyInstancesOf(
            GroupController::class,
            [new GroupController]
        );
    }

    public function testCreateWithExistingGroup(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/groups/',
            [
                'form_params' => [
                    'title' => $this->existingGroup
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => ['The group title not unique']]]));
    }

    public function testIndexWithNonExistentGroup(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/groups/',
            [
                'form_params' => [
                    'title' => $this->nonExistentGroup
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            json_encode(['response' => 'Group ' . ucfirst($this->nonExistentGroup) . ' created.'])
        );
        $group = new Group();
        $group->delete($group->getByTitle($this->nonExistentGroup)[0]);
    }

    public function testShowWithInValidGroupId(): void
    {
        $group_id = 0;
        $response = $this->http->request(
            'GET',
            '/api/groups/users/' . $group_id
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Group not found']]));
    }

    public function testShowWithValidGroupId(): void
    {
        $group = new Group();
        $group_id = $group->all(1)[0]['id'];
        $response = $this->http->request(
            'GET',
            '/api/groups/users/' . $group_id
        );
        $response = json_decode($response->getBody()->getContents(), true)['response'];
        $this->assertTrue(array_key_exists('email', $response[0]));
    }
}
