<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers Orm\RepositoryContainer::get
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_get_Test extends TestCase
{
	public function test()
	{
		$m = new RepositoryContainer;
		$this->assertSame($m, RepositoryContainer::get(NULL));
		$m = new RepositoryContainer;
		$this->assertSame($m, RepositoryContainer::get(NULL));
		$this->assertSame($m, RepositoryContainer::get(NULL));
	}

	public function testNoOne()
	{
		$p = new ReflectionProperty('Orm\RepositoryContainer', 'instance');
		setAccessible($p);
		$p->setValue(NULL);
		$this->setExpectedException('Nette\InvalidStateException', 'RepositoryContainer hasn\'t been instanced yet.');
		RepositoryContainer::get();
	}

	public function testDeprecated1()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Nette\DeprecatedException', 'RepositoryContainer::get() is deprecated do not use it.');
		RepositoryContainer::get();
	}

	public function testDeprecated2()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Nette\DeprecatedException', 'RepositoryContainer::get() is deprecated do not use it.');
		RepositoryContainer::get(true);
	}
}
