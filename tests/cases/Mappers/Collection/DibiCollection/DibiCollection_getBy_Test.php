<?php

use Orm\DibiCollection;

/**
 * @covers Orm\DibiCollection::getBy
 */
class DibiCollection_getBy_Test extends DibiCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(1, false, "SELECT `e`.* FROM `dibicollectionconnected` as e WHERE (`e`.`x` = 'y') LIMIT 1");
		$e = $this->c->getBy(array('x' => 'y'));
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(1, $e->id);
	}

	public function testParentNotChange()
	{
		$this->e(1, false, "SELECT `e`.* FROM `dibicollectionconnected` as e WHERE (`e`.`x` = 'y') LIMIT 1");
		$e = $this->c->getBy(array('x' => 'y'));

		$this->e(1, false, "SELECT `e`.* FROM `dibicollectionconnected` as e");
		$e = $this->c->fetch();

		$this->assertSame($e, $e);
	}

	public function testRelease()
	{
		$this->e(1, false, "SELECT `e`.* FROM `dibicollectionconnected` as e");
		$e = $this->c->fetch();

		$this->e(1, false, "SELECT `e`.* FROM `dibicollectionconnected` as e WHERE (`e`.`x` = 'y') LIMIT 1");
		$e = $this->c->getBy(array('x' => 'y'));

		$this->assertSame($e, $e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getBy');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
