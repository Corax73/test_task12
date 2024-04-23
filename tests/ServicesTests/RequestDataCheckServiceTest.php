<?php

namespace Tests\ServicesTests;

require_once 'config/const.php';

use Models\Group;
use Models\GroupRights;
use Models\User;
use PHPUnit\Framework\TestCase;
use Service\RequestDataCheck;

class RequestDataCheckServiceTest extends TestCase
{
    private $requestDataCheck;
    private $nonExistentEmail;
    private $existingEmail;
    private $invalidShortPassword;
    private $invalidSymbolsInPassword;
    private $validPassword;

    protected function setUp(): void
    {
        $this->requestDataCheck = new RequestDataCheck();
        $this->nonExistentEmail = 'phpunit@mail.php';
        $user = new User();
        $this->existingEmail = $user->all(1)[0]['email'];
        $this->invalidShortPassword = '123';
        $this->invalidSymbolsInPassword = '123abcd?';
        $this->validPassword = '1234Abcd';
    }

    protected function tearDown(): void
    {
        $this->requestDataCheck = NULL;
        $this->nonExistentEmail = NULL;
        $this->existingEmail = NULL;
        $this->invalidShortPassword = NULL;
        $this->invalidSymbolsInPassword = NULL;
        $this->validPassword = NULL;
    }

    public function testCreateGroup(): void
    {
        $this->assertContainsOnlyInstancesOf(
            RequestDataCheck::class,
            [new RequestDataCheck]
        );
    }

    public function testCheckEmailUniquenessWithInvalidEmail(): void
    {
        $this->assertTrue($this->requestDataCheck->checkEmailUniqueness($this->nonExistentEmail));
    }

    public function testCheckEmailUniquenessWithValidEmail(): void
    {
        $this->assertFalse($this->requestDataCheck->checkEmailUniqueness($this->existingEmail));
    }

    public function testCheckingPasswordWithInvalidShortPassword(): void
    {
        $this->assertFalse($this->requestDataCheck->checkingPassword($this->invalidShortPassword));
    }

    public function testCheckingPasswordWithInvalidSymbolsInPassword(): void
    {
        $this->assertFalse($this->requestDataCheck->checkingPassword($this->invalidSymbolsInPassword));
    }

    public function testCheckingPasswordWithValidPassword(): void
    {
        $this->assertTrue($this->requestDataCheck->checkingPassword($this->validPassword));
    }
}
