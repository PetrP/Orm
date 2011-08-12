<?php

/**
 * @covers Orm\DataSourceCollection::getResult
 */
class DataSourceCollection_getResult_Test extends DataSourceCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(0, false);
		$r = $this->c->getResult();
		$this->assertInstanceOf('DibiResult', $r);
		$this->assertSame($r, $this->c->getResult());
	}

	public function testCache()
	{
		$this->e(0, false);
		$this->assertSame($this->c->getResult(), $this->c->getResult());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getResult');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
