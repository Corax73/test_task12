<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\Group;
use Models\GroupRights;
use Models\User;
use Models\UserMembership;
use PHPUnit\Framework\TestCase;

class UserMembershipModelTest extends TestCase
{
    private $userMembership;
    private $validUserId;
    private $invalidUserId;
    private $validGroupId;
    private $invalidGroupId;

    protected function setUp(): void
    {
        $this->userMembership = new UserMembership();
        $user = new User();
        $this->validUserId = $user->all(1)[0]['id'];
        $group = new Group();
        $this->validGroupId = $group->all(1)[0]['id'];
        $this->invalidUserId = 0;
        $this->invalidGroupId = 0;
    }

    protected function tearDown(): void
    {
        $this->userMembership = NULL;
        $this->validGroupId = NULL;
        $this->invalidUserId = NULL;
        $this->validUserId = NULL;
        $this->invalidGroupId = NULL;
    }

    public function testCreateUserMembership(): void
    {
        $this->assertContainsOnlyInstancesOf(
            UserMembership::class,
            [new UserMembership]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('user_memberships', $this->userMembership->getTable());
    }

    public function testSave(): void
    {
        $this->userMembership->save($this->validUserId, $this->validGroupId);
        $data = $this->userMembership->all(1)[0];
        $this->userMembership->delete($this->validUserId, $this->validGroupId);
        $this->assertTrue(count($data) > 0 && in_array($this->validUserId, $data));
    }

    public function testMembershipsWithInvalidUserId(): void
    {
        $this->assertFalse(count($this->userMembership->memberships($this->invalidUserId)) > 0);
    }

    public function testMembershipWithValidUserId(): void
    {
        $this->userMembership->save($this->validUserId, $this->validGroupId);
        $data = $this->userMembership->memberships($this->validUserId)[0];
        $this->assertTrue(count($data) > 0 && in_array($this->validGroupId, $data));
        $this->userMembership->delete($this->validUserId, $this->validGroupId);
    }

    public function testUsersWithInvalidGroupId(): void
    {
        $this->assertFalse(count($this->userMembership->users($this->invalidGroupId)) > 0);
    }

    public function testUsersWithValidGroupId(): void
    {
        $this->userMembership->save($this->validUserId, $this->validGroupId);
        $data = $this->userMembership->users($this->validGroupId);
        $this->assertTrue(count($data) > 0 && in_array($this->validUserId, $data));
        $this->userMembership->delete($this->validUserId, $this->validGroupId);
    }

    public function testDeleteWithInvalidId(): void
    {
        $this->assertFalse($this->userMembership->delete($this->invalidUserId, $this->invalidGroupId));
    }

    public function testDeleteWithValidId(): void
    {
        $this->userMembership->save($this->validUserId, $this->validGroupId);
        $result = $this->userMembership->delete($this->validUserId, $this->validGroupId);
        $this->assertTrue($result);
    }
}
