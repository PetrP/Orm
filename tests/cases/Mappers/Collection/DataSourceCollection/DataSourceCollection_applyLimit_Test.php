<?php

/**
 * @covers Orm\DataSourceCollection::applyLimit
 */
class DataSourceCollection_applyLimit_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(10, 'limit', $this->c);
		$this->assertAttributeSame(20, 'offset', $this->c);
	}

	/**
	 * @covers Orm\DataSourceCollection::release
	 * @covers Orm\BaseDibiCollection::release
	 */
	public function testWipe()
	{
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DataSourceCollection_DataSourceCollection::set($this->c, 'count', 666);
		DataSourceCollection_DataSourceCollection::set($this->c, 'dataSource', $this->c);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->assertAttributeSame($this->c, 'dataSource', $this->c);
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(NULL, 'count', $this->c);
		$this->assertAttributeSame(NULL, 'dataSource', $this->c);

	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->applyLimit(10, 20));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'applyLimit');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
