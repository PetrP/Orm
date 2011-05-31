<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityValue::onAttach
 */
class EntityValue_onAttach_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function test()
	{
		$e = new TestEntity;
		$rule = $this->readAttribute($e, 'rules');
		$e->___event($e, 'attach', $this->r);
		$this->assertAttributeSame($rule, 'rules', $e);
	}

	public function testError()
	{
		$e = new EntityValue_onAttach_Entity;
		$this->setExpectedException('Nette\InvalidStateException', 'fooBar isn\'t repository in EntityValue_onAttach_Entity::$mixed');
		$e->___event($e, 'attach', $this->r);
	}

}
