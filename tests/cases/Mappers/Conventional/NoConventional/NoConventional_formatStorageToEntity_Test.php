<?php

use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::formatStorageToEntity
 */
class NoConventional_formatStorageToEntity_Test extends TestCase
{

	private $c;
	private $a;
	protected function setUp()
	{
		$this->a = array('x' => new Directory, 'y' => 'asdasd');;
		$this->c = new NoConventional;
	}

	public function testBase()
	{
		$this->assertSame($this->a, $this->c->formatStorageToEntity($this->a));
	}

	public function testToArray()
	{
		$this->assertSame($this->a, $this->c->formatStorageToEntity(new ArrayObject($this->a)));
	}

	public function testId()
	{
		$this->assertSame(array('id' => 123), $this->c->formatStorageToEntity(array('id' => 123)));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\NoConventional', 'formatStorageToEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
