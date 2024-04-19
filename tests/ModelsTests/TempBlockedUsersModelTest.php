<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\TempBlockedUsers;
use PHPUnit\Framework\TestCase;

class TempBlockedUsersModelTest extends TestCase
{
    private $tempBlockedUsers;
    private $testUserId;

    protected function setUp(): void
    {
        $this->tempBlockedUsers = new TempBlockedUsers();
        $this->testUserId = 0;
    }

    protected function tearDown(): void
    {
        $this->tempBlockedUsers = NULL;
        $this->testUserId = NULL;
    }

    public function testCreateTempBlockedUsers(): void
    {
        $this->assertContainsOnlyInstancesOf(
            TempBlockedUsers::class,
            [new TempBlockedUsers]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('temp_blocked_users', $this->tempBlockedUsers->getTable());
    }

    public function testSave(): void
    {
        $this->tempBlockedUsers->save($this->testUserId);
        $data = $this->tempBlockedUsers->all(1)[0];
        $this->tempBlockedUsers->delete($this->testUserId);
        $this->assertTrue(count($data) > 0 && in_array($this->testUserId, $data));
    }

    public function testDeleteWithInvalidRight(): void
    {
        $this->assertFalse($this->tempBlockedUsers->delete($this->testUserId));
    }

    public function testDeleteWithValidRight(): void
    {
        $this->tempBlockedUsers->save($this->testUserId);
        $result = $this->tempBlockedUsers->delete($this->testUserId);
        $this->assertTrue($result);
    }
}
