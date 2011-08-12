<?php

/**
 * @covers Orm\SqlConventional::getPrimaryKey
 */
class SqlConventional_getPrimaryKey_Test extends TestCase
{

	public function test()
	{
		$c = new MockSqlConventional;
		$this->assertSame('id', $c->getPrimaryKey());
	}

	/**
	 * @covers Orm\SqlConventional::__construct
	 */
	public function testEntityToStorage()
	{
		$c = new SqlConventional_getPrimaryKey_SqlConventional;
		$this->assertSame(array(
			'aaa_bbb' => 'foo',
			'foo_bar' => 123,
			'bbb_aaa' => 'bar',
		), $c->formatEntityToStorage(array(
			'aaaBbb' => 'foo',
			'id' => 123,
			'bbbAaa' => 'bar',
		)));
	}

	/**
	 * @covers Orm\SqlConventional::__construct
	 */
	public function testStorageToEntity()
	{
		$c = new SqlConventional_getPrimaryKey_SqlConventional;
		$this->assertSame(array(
			'aaaBbb' => 'foo',
			'id' => 123,
			'bbbAaa' => 'bar',
		), $c->formatStorageToEntity(array(
			'aaa_bbb' => 'foo',
			'foo_bar' => 123,
			'bbb_aaa' => 'bar',
		)));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\NoConventional', 'getPrimaryKey');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
