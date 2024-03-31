<?php

namespace Controllers;

require_once 'config/const.php';

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
        $arrUsers = json_decode($response->getBody()->getContents(), true)['response'];
        $this->assertCount(12, $arrUsers);
    }
}
