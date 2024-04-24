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
    private $nonExistentGroupTitle;
    private $existingGroupTitle;
    private $nonExistentEntity;
    private $existingEntity;
    protected function setUp(): void
    {
        $this->requestDataCheck = new RequestDataCheck();
        $this->nonExistentEmail = 'phpunit@mail.php';
        $user = new User();
        $this->existingEmail = $user->all(1)[0]['email'];
        $this->invalidShortPassword = '123';
        $this->invalidSymbolsInPassword = '123abcd?';
        $this->validPassword = '1234Abcd';
        $this->nonExistentGroupTitle = 'php-unit';
        $group = new Group();
        $this->existingGroupTitle = $group->all(1)[0]['title'];
        $this->nonExistentEntity = 'php-unit';
        $this->existingEntity = 'user';
    }

    protected function tearDown(): void
    {
        $this->requestDataCheck = NULL;
        $this->nonExistentEmail = NULL;
        $this->existingEmail = NULL;
        $this->invalidShortPassword = NULL;
        $this->invalidSymbolsInPassword = NULL;
        $this->validPassword = NULL;
        $this->nonExistentGroupTitle = NULL;
        $this->existingGroupTitle = NULL;
        $this->nonExistentEntity = NULL;
        $this->existingEntity = NULL;
    }

    public function testCreateGroup(): void
    {
        $this->assertContainsOnlyInstancesOf(
            RequestDataCheck::class,
            [new RequestDataCheck]
        );
    }

    public function testCheckEmailUniquenessWithNonExistentEmail(): void
    {
        $this->assertTrue($this->requestDataCheck->checkEmailUniqueness($this->nonExistentEmail));
    }

    public function testCheckEmailUniquenessWithExistingEmail(): void
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

    public function testCheckGroupTitleUniquenessWithNonExistentGroupTitle(): void
    {
        $this->assertTrue($this->requestDataCheck->checkGroupTitleUniqueness($this->nonExistentGroupTitle));
    }

    public function testCheckGroupTitleUniquenessWithExistingGroupTitle(): void
    {
        $this->assertFalse($this->requestDataCheck->checkGroupTitleUniqueness($this->existingGroupTitle));
    }

    public function testCheckEntityExistWithNonExistentEntity(): void
    {
        $this->assertFalse($this->requestDataCheck->checkEntityExist($this->nonExistentEntity));
    }

    public function testCheckEntityExistWithExistingEntity(): void
    {
        $this->assertTrue($this->requestDataCheck->checkEntityExist($this->existingEntity));
    }
}
