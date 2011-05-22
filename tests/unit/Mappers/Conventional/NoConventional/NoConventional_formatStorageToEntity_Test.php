<?php

use Orm\NoConventional;
use Nette\Utils\Html;

require_once dirname(__FILE__) . '/../../../../boot.php';

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

}
