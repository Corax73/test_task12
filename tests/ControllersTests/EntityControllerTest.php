<?php

namespace Controllers;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class EntityControllerTest extends TestCase
{
    private $http;
    private $validTarget;
    private $invalidTarget;
    private $offset;

    protected function setUp(): void
    {
        $this->http = new Client(['base_uri' => 'http://localhost:8000']);
        $this->validTarget = 'user';
        $this->invalidTarget = 'user1';
        $this->offset = 12;
    }

    protected function tearDown(): void
    {
        $this->http = NULL;
        $this->validTarget = NULL;
        $this->invalidTarget = NULL;
        $this->offset = NULL;
    }

    public function testIndexWithInvalidTarget(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/entities/' . $this->invalidTarget
        );
        $this->assertJsonStringEqualsJsonString($response->getBody()->getContents(), json_encode(['response' => ['errors' => 'Entity not found']]));
    }

    public function testIndexWithValidTarget(): void
    {
        $response = $this->http->request(
            'GET',
            '/api/entities/' . $this->validTarget
        );
        $users1 = json_decode($response->getBody()->getContents(), true)['response'];
        $this->assertCount(12, $users1);
    }

    public function testIndexWithValidTargetAndOffset(): void
    {
        $response1 = $this->http->request(
            'GET',
            '/api/entities/' . $this->validTarget
        );
        $response2 = $this->http->request(
            'GET',
            '/api/entities/' . $this->validTarget . '/' . $this->offset
        );
        $users1 = json_decode($response1->getBody()->getContents(), true)['response'];
        $users2 = json_decode($response2->getBody()->getContents(), true)['response'];
        $this->assertTrue(count(array_diff($users1[0], $users2[0])) > 0);
    }
}
