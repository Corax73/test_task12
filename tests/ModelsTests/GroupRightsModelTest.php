<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\Group;
use Models\GroupRights;
use PHPUnit\Framework\TestCase;

class GroupRightsModelTest extends TestCase
{
    private $groupRights;
    private $invalidGroupId;
    private $validGroupId;

    protected function setUp(): void
    {
        $this->groupRights = new GroupRights();
        $this->invalidGroupId = 0;
        $group = new Group();
        $this->validGroupId = $group->all(1)[0]['id'];
    }

    protected function tearDown(): void
    {
        $this->groupRights = NULL;
        $this->invalidGroupId = NULL;
        $this->validGroupId = NULL;
    }

    public function testCreateGroupRights(): void
    {
        $this->assertContainsOnlyInstancesOf(
            GroupRights::class,
            [new GroupRights]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('group_rights', $this->groupRights->getTable());
    }

    public function testSave(): void
    {
        $this->groupRights->save($this->validGroupId, 'php-unit');
        $data = collect($this->groupRights->getRights($this->validGroupId))->map(fn($rights) => $rights['right_name'])->toArray();
        $this->groupRights->delete($this->validGroupId, 'php-unit');
        $this->assertTrue(count($data) > 0 && in_array('php-unit', $data));
    }

    public function testGetRightsWithInvalidGroupId(): void
    {
        $this->assertFalse(count($this->groupRights->getRights($this->invalidGroupId)) > 0);
    }

    public function testGetRightsWithValidGroupId(): void
    {
        $this->assertTrue(count($this->groupRights->getRights($this->validGroupId)) > 0);
    }

    public function testDeleteWithInvalidData(): void
    {
        $this->assertFalse($this->groupRights->delete($this->invalidGroupId, 'php-unit'));
    }

    public function testDeleteWithValidData(): void
    {
        $this->groupRights->save($this->validGroupId, 'php-unit');
        $this->assertTrue($this->groupRights->delete($this->validGroupId, 'php-unit'));
    }
}
