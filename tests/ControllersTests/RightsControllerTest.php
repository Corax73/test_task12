<?php

namespace Tests\ControllersTests;

require_once 'config/const.php';

use Controllers\RightsController;
use Enums\ListRights;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Models\Group;
use Models\GroupRights;
use Models\TempBlockedRights;

class RightsControllerTest extends TestCase
{
    private $http;
    private $existingRight;
    private $nonExistentRight;
    private $blockedRights;
    private $group;
    private $groupRights;
    private $tempBlocked;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->existingRight = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
        $this->nonExistentRight = $this->existingRight[0] . '1';
        $this->group = new Group();
        $this->groupRights = new GroupRights();
        $this->tempBlocked = new TempBlockedRights();
        $this->blockedRights = collect($this->tempBlocked->all())->map(fn ($item) => $item['right_name'])->toArray();
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->existingRight = NULL;
        $this->nonExistentRight = NULL;
        $this->blockedRights = NULL;
        $this->group = NULL;
        $this->groupRights = NULL;
        $this->tempBlocked = NULL;
    }

    public function testCreateRightsController(): void
    {
        $this->assertContainsOnlyInstancesOf(
            RightsController::class,
            [new RightsController]
        );
    }

    public function testCreateWithExistingRight(): void
    {
        $group_id = $this->group->all(1)[0]['id'];
        $establishedRights = collect($this->groupRights->getRights($group_id))->unique()->collapse()->values()->toArray();
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
        $this->groupRights->delete($group_id, $right);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => 'right settled']));
    }

    public function testCreateWithInValidGroupId(): void
    {
        $group_id = $this->group->all(1)[0]['id'];
        $establishedRights = collect($this->groupRights->getRights($group_id))->unique()->collapse()->values()->toArray();
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
        $group_id = $this->group->all(1)[0]['id'];
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
        $group_id = $this->group->all(1)[0]['id'];
        $right = $this->existingRight[0];
        $this->groupRights->save($group_id, $right);
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
        $this->groupRights->delete($group_id, $right);
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
        $group_id = $this->group->all(1)[0]['id'];
        $response = $this->http->request(
            'GET',
            '/api/rights/groups/' . $group_id
        );
        $rights = json_decode($response->getBody()->getContents(), true)['response'];
        $this->assertTrue(count($rights) > 0);
    }

    public function testDestroyWithInValidGroupId(): void
    {
        $group_id = $this->group->all(1)[0]['id'];
        $right = $this->groupRights->all(1)[0]['right_name'];
        $group_id = 0;
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $right
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Group not found']]));
    }

    public function testDestroyWithNonExistentRight(): void
    {
        $group_id = $this->group->all(1)[0]['id'];
        $right = $this->nonExistentRight;
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $right
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => "Right $right not found"]]));
    }

    public function testDestroyWithValidData(): void
    {
        $group_id = $this->group->all(1)[0]['id'];
        $right = $this->existingRight[0];
        $this->groupRights->save($group_id, $right);
        $response = $this->http->request(
            'DELETE',
            '/api/rights/groups/' . $group_id . '/' . $right
        );
        $this->groupRights->save($group_id, $right);
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => 'group right removed']));
    }

    public function testSetTempBlockedRightWithExistingRight(): void
    {
        $right = array_diff($this->existingRight, $this->blockedRights);
        if ($right) {
            $right = array_shift($right);
        }
        $response = $this->http->request(
            'POST',
            '/api/rights/temp-blocked/',
            [
                'form_params' => [
                    'right' => $right
                ]
            ]
        );
        $this->tempBlocked->delete($right);
        $this->assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            json_encode(['response' => "Temporary blocking of the right $right has been established"])
        );
    }

    public function testSetTempBlockedRightWithNonExistingRight(): void
    {
        $response = $this->http->request(
            'POST',
            '/api/rights/temp-blocked/',
            [
                'form_params' => [
                    'right' => $this->nonExistentRight
                ]
            ]
        );
        $this->assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            json_encode(['response' => ['errors' => "Right $this->nonExistentRight not found"]])
        );
    }

    public function testDestroyTemporaryBlockingWithNonExistentRight(): void
    {
        $response = $this->http->request(
            'DELETE',
            '/api/rights/temp-blocked/' . $this->nonExistentRight
        );
        $this->assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            json_encode(['response' => ['errors' => "Right $this->nonExistentRight not found"]])
        );
    }

    public function testDestroyTemporaryBlockingWithExistentRight(): void
    {
        $right = $this->blockedRights[rand(0, count($this->blockedRights) - 1)];
        $response = $this->http->request(
            'DELETE',
            '/api/rights/temp-blocked/' . $right
        );
        $this->tempBlocked->save($right);
        $this->assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            json_encode(['response' => "Temporary blocking of the right $right has been removed."])
        );
    }
}
