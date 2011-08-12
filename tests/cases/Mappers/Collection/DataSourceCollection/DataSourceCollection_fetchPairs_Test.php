<?php

/**
 * @covers Orm\DataSourceCollection::fetchPairs
 */
class DataSourceCollection_fetchPairs_Test extends DataSourceCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(3);
		$all = $this->c->fetchPairs('id', 'string');
		$this->assertSame(array(
			1 => 'boo',
			2 => 'foo',
			3 => 'bar',
		), $all);
	}

	public function testNoRow()
	{
		$this->e(0);
		$all = $this->c->fetchPairs('id', 'string');
		$this->assertSame(array(), $all);
	}

	public function testNull()
	{
		$this->e(3);
		$all = $this->c->fetchPairs(NULL, 'string');
		$this->assertSame(array(
			0 => 'boo',
			1 => 'foo',
			2 => 'bar',
		), $all);
	}

	public function testNullNull()
	{
		$this->e(3);
		$all = $this->c->fetchPairs();
		$this->assertSame(array(
			1 => 'boo',
			2 => 'foo',
			3 => 'bar',
		), $all);
	}

	public function testNotNullNull()
	{
		$this->e(1, false);
		$this->setExpectedException('InvalidArgumentException', 'Either none or both columns must be specified.');
		$all = $this->c->fetchPairs('id');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'fetchPairs');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
