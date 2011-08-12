<?php

use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::getPrimaryKey
 */
class NoConventional_getPrimaryKey_Test extends TestCase
{

	public function test()
	{
		$c = new NoConventional;
		$this->assertSame('id', $c->getPrimaryKey());
	}

	/**
	 * @covers Orm\NoConventional::__construct
	 * @covers Orm\NoConventional::formatEntityToStorage
	 * @covers Orm\NoConventional::renameKey
	 */
	public function testEntityToStorage()
	{
		$c = new NoConventional_getPrimaryKey_NoConventional;
		$this->assertSame(array(
			'aaaBbb' => 'foo',
			'foo_bar' => 123,
			'bbbAaa' => 'bar',
		), $c->formatEntityToStorage(array(
			'aaaBbb' => 'foo',
			'id' => 123,
			'bbbAaa' => 'bar',
		)));
	}

	/**
	 * @covers Orm\NoConventional::__construct
	 * @covers Orm\NoConventional::formatStorageToEntity
	 * @covers Orm\NoConventional::renameKey
	 */
	public function testStorageToEntity()
	{
		$c = new NoConventional_getPrimaryKey_NoConventional;
		$this->assertSame(array(
			'aaaBbb' => 'foo',
			'id' => 123,
			'bbbAaa' => 'bar',
		), $c->formatStorageToEntity(array(
			'aaaBbb' => 'foo',
			'foo_bar' => 123,
			'bbbAaa' => 'bar',
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
