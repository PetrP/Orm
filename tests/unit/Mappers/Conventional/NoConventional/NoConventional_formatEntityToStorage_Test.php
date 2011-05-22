<?php

use Orm\NoConventional;
use Nette\Utils\Html;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\NoConventional::formatEntityToStorage
 */
class NoConventional_formatEntityToStorage_Test extends TestCase
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
		$this->assertSame($this->a, $this->c->formatEntityToStorage($this->a));
	}

	public function testToArray()
	{
		$this->assertSame($this->a, $this->c->formatEntityToStorage(new ArrayObject($this->a)));
	}

}
