<?php

use Orm\NoConventional;

/**
 * @covers Orm\NoConventional::formatEntityToStorage
 */
class NoConventional_formatEntityToStorage_Test extends TestCase
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
		$this->assertSame($this->a, $this->c->formatEntityToStorage($this->a));
	}

	public function testToArray()
	{
		$this->assertSame($this->a, $this->c->formatEntityToStorage(new ArrayObject($this->a)));
	}

}
