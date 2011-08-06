<?php

use Orm\NoConventional;
use Nette\Utils\Html;

/**
 * @covers Orm\NoConventional::formatStorageToEntity
 */
class NoConventional_formatStorageToEntity_Test extends TestCase
{

	private $c;
	private $a;
	protected function setUp()
	{
		$this->a = array('x' => new Html, 'y' => 'asdasd');;
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

}
