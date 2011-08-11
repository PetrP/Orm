<?php

/**
 * @covers Orm\DibiCollection::count
 */
class DibiCollection_count_Test extends DibiCollection_BaseConnected_Test
{

	public function test()
	{
		$this->d->addExpected('query', true, 'SELECT `e`.* FROM `dibicollectionconnected` as e');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getRowCount', 3);
		$this->assertSame(3, $this->c->count());
		$this->assertSame(3, $this->c->count()); // cache
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiCollection', 'count');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
