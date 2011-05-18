<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::setValueHelper
 * @see EntityValue_getter_Test
 * @see EntityValue_injection_Test
 */
class EntityValue_setValueHelper_Test extends TestCase
{
	private $e;
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new EntityValue_getset_Entity;
		$this->r = $m->TestEntity;
	}

	public function testFkOk()
	{
		$fke = $this->r->getById(1);
		$this->e->fk = $fke->id;
		$this->assertSame($fke->id, $this->e->fk->id);
	}

	public function testFkNull()
	{
		$this->setExpectedException('UnexpectedValueException', "Param EntityValue_getset_Entity::\$fk must be 'testentity', 'NULL' given");
		$this->e->fk;
	}

	public function testFkBadValue()
	{
		$this->setExpectedException('UnexpectedValueException', "Entity(testentity) 'xxx' not found in `TestEntityRepository` in EntityValue_getset_Entity::\$fk");
		$this->e->fk = 'xxx';
	}

	public function testFkOrNull()
	{
		$this->e->fk2 = 'xxx';
		$this->assertSame(NULL, $this->e->fk2);
		$this->e->fk2 = NULL;
		$this->assertSame(NULL, $this->e->fk2);
	}

}
