<?php

/**
 * @covers Orm\DataSourceCollection::getResultWrapper
 */
class DataSourceCollection_getResultWrapper_Test extends DataSourceCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(0, false);
		$r = $this->c->getResultWrapper();
		$this->assertInstanceOf('Orm\DibiResultWrapper', $r);
		$this->assertSame($r, $this->c->getResultWrapper());
	}

	public function testCache()
	{
		$this->e(0, false);
		$this->assertSame($this->c->getResultWrapper(), $this->c->getResultWrapper());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getResultWrapper');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
