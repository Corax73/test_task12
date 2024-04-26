<?php

namespace Tests\ServicesTests;

require_once 'config/const.php';

use Models\Group;
use Models\GroupRights;
use Models\User;
use Models\UserMembership;
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
    private $invalidGroupId;
    private $validGroupId;
    private $existingRight;
    private $nonExistentRight;
    private $validGroupIdFromUserMembership;
    private $validUserIdFromUserMembership;
    private $invalidUserId;

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
        $this->invalidGroupId = 0;
        $groupRights = new GroupRights();
        $data = $groupRights->all(1)[0];
        $this->validGroupId = $data['group_id'];
        $this->existingRight = $data['right_name'];
        $this->nonExistentRight = $data['right_name'] . '1';
        $userMembership = new UserMembership();
        $data = $userMembership->all(1)[0];
        $this->validGroupIdFromUserMembership = $data['group_id'];
        $this->validUserIdFromUserMembership = $data['user_id'];
        $this->invalidUserId = 0;
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
        $this->invalidGroupId = NULL;
        $this->validGroupId = NULL;
        $this->existingRight = NULL;
        $this->nonExistentRight = NULL;
        $this->validGroupIdFromUserMembership = NULL;
        $this->validUserIdFromUserMembership = NULL;
        $this->invalidUserId = NULL;
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

    public function testCheckGroupHasRightWithInvalidGroupId(): void
    {
        $this->assertFalse($this->requestDataCheck->checkGroupHasRight($this->invalidGroupId, $this->existingRight));
    }

    public function testCheckGroupHasRightWithValidGroupId(): void
    {
        $this->assertTrue($this->requestDataCheck->checkGroupHasRight($this->validGroupId, $this->existingRight));
    }

    public function testCheckGroupHasRightWithNonExistentRight(): void
    {
        $this->assertFalse($this->requestDataCheck->checkGroupHasRight($this->validGroupId, $this->nonExistentRight));
    }

    public function testCheckGroupHasUserWithInvalidGroupId(): void
    {
        $this->assertFalse($this->requestDataCheck->checkGroupHasUser($this->invalidGroupId, $this->validUserIdFromUserMembership));
    }

    public function testCheckGroupHasUserWithValidGroupId(): void
    {
        $this->assertTrue($this->requestDataCheck->checkGroupHasUser($this->validGroupIdFromUserMembership, $this->validUserIdFromUserMembership));
    }

    public function testCheckGroupHasUserWithInvalidUserId(): void
    {
        $this->assertFalse($this->requestDataCheck->checkGroupHasUser($this->validGroupId, $this->invalidUserId));
    }
}
