<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers ManyToMany::setInjectedValue
 */
class ManyToMany_setInjectedValue_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$m = new Model;
		$this->m = new ManyToMany($m->TestEntity->getById(1), $m->TestEntity);
	}

	public function testNull()
	{
		$this->m->setInjectedValue(array(1, 2));
		$this->m->setInjectedValue(NULL);
		$this->assertSame(2, count($this->m->get()));
	}

}
