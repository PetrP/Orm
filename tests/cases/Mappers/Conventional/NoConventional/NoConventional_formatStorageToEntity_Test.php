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

}
