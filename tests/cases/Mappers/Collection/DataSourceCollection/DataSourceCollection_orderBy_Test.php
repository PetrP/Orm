<?php

/**
 * @covers Orm\DataSourceCollection::orderBy
 */
class DataSourceCollection_orderBy_Test extends DataSourceCollection_Base_Test
{

	public function testResetResult()
	{
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DataSourceCollection_DataSourceCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
	}

	public function testBase()
	{
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(array(array('xxx', Dibi::ASC)), 'sorting', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->orderBy('xxx'));
	}

	public function testConventional()
	{
		$this->c->orderBy('fooBarFoo');
		$this->assertAttributeSame(array(array('foo_bar_foo', Dibi::ASC)), 'sorting', $this->c);
	}

	public function testMore()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('yyy');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('yyy', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testMoreSame()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testAsc()
	{
		$this->c->orderBy('xxx', Dibi::ASC);
		$this->c->orderBy('xxx', 'ASC');
		$this->c->orderBy('xxx', 'asc');
		$this->c->orderBy('xxx', 'aSc');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testDesc()
	{
		$this->c->orderBy('xxx', Dibi::DESC);
		$this->c->orderBy('xxx', 'DESC');
		$this->c->orderBy('xxx', 'desc');
		$this->c->orderBy('xxx', 'dEsC');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
		), 'sorting', $this->c);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Orm\DataSourceCollection::orderBy() $direction must be Orm\IEntityCollection::ASC or DESC; \'bad\' given.');
		$this->c->orderBy('xxx', 'bad');
	}

	/**
	 * @covers Orm\DataSourceCollection::release
	 * @covers Orm\BaseDibiCollection::release
	 */
	public function testWipe()
	{
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DataSourceCollection_DataSourceCollection::set($this->c, 'count', 555);
		DataSourceCollection_DataSourceCollection::set($this->c, 'dataSource', $this->c);
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(555, 'count', $this->c);
		$this->assertAttributeSame(NULL, 'dataSource', $this->c);
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DataSourceCollection_DataSourceCollection::set($this->c, 'count', 666);
		DataSourceCollection_DataSourceCollection::set($this->c, 'dataSource', $this->c);
		$this->assertNotEmpty($this->readAttribute($this->c, 'sorting'));
		$this->c->orderBy(array());
		$this->assertEmpty($this->readAttribute($this->c, 'sorting'));
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->assertAttributeSame(NULL, 'dataSource', $this->c);
	}

	public function testArray()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->c->orderBy(array('aaA' => Dibi::ASC, 'bBb' => Dibi::DESC));
		$this->assertAttributeSame(array(
			array('aa_a', Dibi::ASC),
			array('b_bb', Dibi::DESC),
		), 'sorting', $this->c);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'orderBy');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
