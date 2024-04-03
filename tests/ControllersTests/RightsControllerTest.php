<?php

namespace Controllers;

require_once 'config/const.php';

use Enums\ListRights;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;
use Models\GroupRights;

class RightsControllerTest extends TestCase
{
    private $http;
    private $existingRight;
    private $nonExistentRight;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->existingRight = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
        $this->nonExistentRight = $this->existingRight[0] . '1';
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->existingRight = NULL;
        $this->nonExistentRight = NULL;
    }

    public function testCreateWithExistingRight(): void
    {
        $group = new Group();
        $groupRights = new GroupRights();
        $group_id = $group->all(1)[0]['id'];
        $establishedRights = collect($groupRights->getRights($group_id))->unique()->collapse()->values()->toArray();
        $right = array_diff($this->existingRight, $establishedRights);
        if ($right) {
            $right = array_shift($right);
        }
        $response = $this->http->request(
            'POST',
            '/api/rights/groups/',
            [
                'form_params' => [
                    'group_id' => $group_id,
                    'right' => $right
                ]
            ]
        );
        $groupRights = new GroupRights();
        $groupRights->delete($group_id, $right);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => 'right settled']));
    }

    public function testCreateWithInValidGroupId(): void
    {
        $group = new Group();
        $groupRights = new GroupRights();
        $group_id = $group->all(1)[0]['id'];
        $establishedRights = collect($groupRights->getRights($group_id))->unique()->collapse()->values()->toArray();
        $group_id = 0;
        $right = array_diff($this->existingRight, $establishedRights);
        if ($right) {
            $right = array_shift($right);
        }
        $response = $this->http->request(
            'POST',
            '/api/rights/groups/',
            [
                'form_params' => [
                    'group_id' => $group_id,
                    'right' => $right
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Group not found']]));
    }

    public function testCreateWithNonExistentRight(): void
    {
        $group = new Group();
        $group_id = $group->all(1)[0]['id'];
        $right = $this->nonExistentRight;
        $response = $this->http->request(
            'POST',
            '/api/rights/groups/',
            [
                'form_params' => [
                    'group_id' => $group_id,
                    'right' => $right
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => "Right $right not found"]]));
    }

    public function testCreateWithEstablishedRight(): void
    {
        $group = new Group();
        $groupRights = new GroupRights();
        $group_id = $group->all(1)[0]['id'];
        $right = collect($groupRights->getRights($group_id))->unique()->collapse()->values()->toArray()[0];
        $response = $this->http->request(
            'POST',
            '/api/rights/groups/',
            [
                'form_params' => [
                    'group_id' => $group_id,
                    'right' => $right
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => "Right $right already available"]]));
    }

    public function testShowWithInValidGroupId(): void
    {
        $group_id = 0;
        $response = $this->http->request(
            'GET',
            '/api/rights/groups/' . $group_id
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Rights not found']]));
    }

    public function testShowWithValidGroupId(): void
    {
        $group = new Group();
        $group_id = $group->all(1)[0]['id'];
        $response = $this->http->request(
            'GET',
            '/api/rights/groups/' . $group_id
        );
        $rights = json_decode($response->getBody()->getContents(), true)['response'];
        $this->assertTrue(count($rights) > 0);
    }

    public function testDestroyWithInValidGroupId(): void
    {
        $group = new Group();
        $groupRights = new GroupRights();
        $group_id = $group->all(1)[0]['id'];
        $right = collect($groupRights->getRights($group_id))->unique()->collapse()->values()->toArray()[0];
        $group_id = 0;
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $right
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Group not found']]));
    }

    public function testDestroyWithNonExistentRight(): void
    {
        $group = new Group();
        $group_id = $group->all(1)[0]['id'];
        $right = $this->nonExistentRight;
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $right
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => "Right $right not found"]]));
    }

    public function testDestroyWithValidData(): void
    {
        $group = new Group();
        $groupRights = new GroupRights();
        $group_id = $group->all(1)[0]['id'];
        $establishedRights = collect($groupRights->getRights($group_id))->unique()->collapse()->values()->toArray();
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $establishedRights[0]
        );
        $groupRights->save($group_id, $establishedRights[0]);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => 'group right removed']));
    }
}
