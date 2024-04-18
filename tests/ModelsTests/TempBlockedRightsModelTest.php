<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\TempBlockedRights;
use PHPUnit\Framework\TestCase;

class TempBlockedRightsModelTest extends TestCase
{
    private $tempBlockedRights;
    private $testRight;

    protected function setUp(): void
    {
        $this->tempBlockedRights = new TempBlockedRights();
        $this->testRight = 'php-unit';
    }

    protected function tearDown(): void
    {
        $this->tempBlockedRights = NULL;
        $this->testRight = NULL;
    }

    public function testCreateGroupRights(): void
    {
        $this->assertContainsOnlyInstancesOf(
            TempBlockedRights::class,
            [new TempBlockedRights]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('temp_blocked', $this->tempBlockedRights->getTable());
    }

    public function testSave(): void
    {
        $this->tempBlockedRights->save($this->testRight);
        $data = $this->tempBlockedRights->all(1)[0];
        $this->tempBlockedRights->delete('php-unit');
        $this->assertTrue(count($data) > 0 && in_array('php-unit', $data));
    }

    public function testDeleteWithInvalidRight(): void
    {
        $this->assertFalse($this->tempBlockedRights->delete($this->testRight));
    }

    public function testDeleteWithValidRight(): void
    {
        $validRight = $this->tempBlockedRights->all(1)[0]['right_name'];
        $result = $this->tempBlockedRights->delete($validRight);
        $this->tempBlockedRights->save($validRight);
        $this->assertTrue($result);
    }
}
