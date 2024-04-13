<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use PHPUnit\Framework\TestCase;
use Models\Connect;
use PDO;

class ConnectModelTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new Connect();
    }

    protected function tearDown(): void
    {
        $this->conn = NULL;
    }

    public function testCreateConnect(): void
    {
        $this->assertContainsOnlyInstancesOf(
            Connect::class,
            [new Connect]
        );
    }

    public function testReturnType(): void
    {
        $connect = $this->createMock(Connect::class);
        $result = $connect->connect(PATH_CONF);
        $this->assertContainsOnlyInstancesOf(
            PDO::class,
            [$result]
        );
    }
}
