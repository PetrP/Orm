<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityValue::getValue
 * @see EntityValue_getter_Test
 */
class EntityValue_getValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property EntityValue_getset_Entity::$unexists.');
		$this->e->gv('unexists');
	}

	public function test()
	{
		$this->e->string = 'xyz';
		$this->assertSame('xyz', $this->e->gv('string'));
	}

	public function testNotReadable()
	{
		if (PHP_VERSION_ID < 50300)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
		}
		$p = new ReflectionProperty('Orm\_EntityValue', 'rules');
		$p->setAccessible(true);
		$rules = $p->getValue($this->e);
		$rules['id']['get'] = NULL;
		$p->setValue($this->e, $rules);
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read to a write-only property EntityValue_getset_Entity::$id.');
		$this->e->___event($this->e, 'persist', new TestsRepository(new RepositoryContainer), 2);
	}

}
