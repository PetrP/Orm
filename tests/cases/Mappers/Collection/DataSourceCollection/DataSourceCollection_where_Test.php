<?php

/**
 * @covers Orm\DataSourceCollection::where
 */
class DataSourceCollection_where_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->c->where('1=1');
		$this->assertAttributeSame(array(array('1=1')), 'where', $this->c);
		$this->c->where('2=2');
		$this->assertAttributeSame(array(array('1=1'), array('2=2')), 'where', $this->c);
	}

	public function testArray()
	{
		$this->c->where(array('`bb` = `aa`'), 'lost');
		$this->assertAttributeSame(array(array('`bb` = `aa`')), 'where', $this->c);
	}

	public function testMoreParams()
	{
		$this->c->where('%n = %s', 'foo', 'bar');
		$this->assertAttributeSame(array(array('%n = %s', 'foo', 'bar')), 'where', $this->c);
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
		$this->c->where('1=1');
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(NULL, 'count', $this->c);
		$this->assertAttributeSame(NULL, 'dataSource', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->where('1=1'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'where');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
