<?php

use Orm\ArrayCollection;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::fetch
 */
class ArrayCollection_fetch_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$this->assertSame($this->e[0], $this->c->fetch());
		$this->assertSame($this->e[0], $this->c->fetch());
		$this->assertSame($this->e[0], $this->c->fetch());
	}

	public function testEmpty()
	{
		$c = new ArrayCollection(array());
		$this->assertSame(NULL, $c->fetch());
	}

	public function testEmpty2()
	{
		$this->c->applyLimit(0);
		$this->assertSame(NULL, $this->c->fetch());
	}

}
