<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\Group;
use Models\GroupRights;
use PHPUnit\Framework\TestCase;

class GroupModelTest extends TestCase
{
    private $group;
    private $invalidGroupId;
    private $validGroupId;

    protected function setUp(): void
    {
        $this->group = new Group();
        $this->invalidGroupId = 0;
        $this->validGroupId = $this->group->all(1)[0]['id'];
    }

    protected function tearDown(): void
    {
        $this->group = NULL;
        $this->invalidGroupId = NULL;
        $this->validGroupId = NULL;
    }

    public function testCreateGroup(): void
    {
        $this->assertContainsOnlyInstancesOf(
            Group::class,
            [new Group]
        );
    }

    public function testGetTable(): void
    {
        $this->assertEquals('groups', $this->group->getTable());
    }

    public function testFindWithInvalidGroupId(): void
    {
        $this->assertFalse($this->group->find($this->invalidGroupId));
    }

    public function testFindWithValidGroupId(): void
    {
        $this->assertIsArray($this->group->find($this->validGroupId));
    }

    public function testAllByContainedKeys(): void
    {
        $data = $this->group->all();
        $groupKeys = ['id' => 'test', 'title' => 'test', 'created_at' => 'test', 'updated_at' => 'test'];
        $this->assertTrue(count(array_diff_key($groupKeys, $data[0])) === 0);
    }

    public function testSave(): void
    {
        $this->group->save('php-unit');
        $data = $this->group->getByTitle('php-unit');
        $this->group->delete($data[0]);
        $this->assertTrue(count($data) > 0 && in_array('php-unit', $data));
    }

    public function testRightsWithInvalidGroupId(): void
    {
        $this->assertTrue(count($this->group->rights($this->invalidGroupId)) == 0);
    }

    public function testRightsWithValidGroupId(): void
    {
        $groupRights = new GroupRights();
        $idGroupWithRights = $groupRights->all(1)[0]['group_id'];
        $this->assertTrue(count($this->group->rights($idGroupWithRights)) > 0);
    }

    public function testUsersWithInvalidGroupId(): void
    {
        $this->assertTrue(count($this->group->users($this->invalidGroupId)) == 0);
    }

    public function testUsersWithValidGroupId(): void
    {
        $this->assertTrue(count($this->group->users($this->validGroupId)) > 0);
    }

    public function testDeleteWithInvalidGroupId(): void
    {
        $this->assertFalse($this->group->delete($this->invalidGroupId));
    }

    public function testGetByTitleWithInvalidTitle(): void
    {
        $this->assertFalse(count($this->group->getByTitle('php-unit')) > 0);
    }

    public function testGetByTitleWithValidTitle(): void
    {
        $this->assertTrue(count($this->group->getByTitle($this->group->find($this->validGroupId)[0]['title'])) > 0);
    }
}
