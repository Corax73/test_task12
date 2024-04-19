<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    private $user;
    private $testEmail;
    private $testPassword;
    private $invalidUserId;
    private $validUserId;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->testEmail = 'phpunit@mail.php';
        $this->testPassword = '12345678';
        $this->invalidUserId = 0;
        $this->validUserId = $this->user->all(1)[0]['id'];
    }

    protected function tearDown(): void
    {
        $this->user = NULL;
        $this->testEmail = NULL;
        $this->testPassword = NULL;
        $this->invalidUserId = NULL;
        $this->validUserId = NULL;
    }

    public function testCreateUser(): void
    {
        $this->assertContainsOnlyInstancesOf(
            User::class,
            [new User]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('users', $this->user->getTable());
    }

    public function testSave(): void
    {
        $this->user->save($this->testEmail, $this->testPassword);
        $data = $this->user->all(1)[0];
        $id = $this->user->getByEmail($this->testEmail)[0];
        $this->user->delete($id);
        $this->assertTrue(count($data) > 0 && in_array($this->testEmail, $data));
    }

    public function testDeleteWithInvalidRight(): void
    {
        $this->assertFalse($this->user->delete($this->invalidUserId));
    }

    public function testDeleteWithValidRight(): void
    {
        $this->user->save($this->testEmail, $this->testPassword);
        $id = $this->user->getByEmail($this->testEmail)[0];
        $result = $this->user->delete($id);
        $this->assertTrue($result);
    }
}
