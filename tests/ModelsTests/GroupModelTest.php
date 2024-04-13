<?php

namespace Tests\ModelsTests;

require_once 'config/const.php';

use Models\Group;
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
    }

    public function testCreateConnect(): void
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
}
