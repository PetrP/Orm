<?php

use Orm\DibiCollection;

/**
 * @covers Orm\DataSourceCollection::getBy
 */
class DataSourceCollection_getBy_Test extends DataSourceCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(1, false, "SELECT * FROM `datasourcecollectionconnected` WHERE (`x` = 'y') LIMIT 1");
		$e = $this->c->getBy(array('x' => 'y'));
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(1, $e->id);
	}

	public function testParentNotChange()
	{
		$this->e(1, false, "SELECT * FROM `datasourcecollectionconnected` WHERE (`x` = 'y') LIMIT 1");
		$e = $this->c->getBy(array('x' => 'y'));

		$this->e(1, false, "SELECT * FROM `datasourcecollectionconnected`");
		$e = $this->c->fetch();

		$this->assertSame($e, $e);
	}

	public function testRelease()
	{
		$this->e(1, false, "SELECT * FROM `datasourcecollectionconnected`");
		$e = $this->c->fetch();

		$this->e(1, false, "SELECT * FROM `datasourcecollectionconnected` WHERE (`x` = 'y') LIMIT 1");
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
