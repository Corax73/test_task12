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
        if($right) {
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
}
