<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::getResult
 * @covers Orm\ArrayCollection::_sort
 */
class ArrayCollection_getResult_orderBy_Test extends ArrayCollection_Base_Test
{

	public function testAsc()
	{
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[1],
			$this->e[0],
			$this->e[2],
			$this->e[3],
		), $this->c->getResult());
	}

	public function testDesc()
	{
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[2],
			$this->e[0],
			$this->e[1],
		), $this->c->getResult());
	}

	public function testMultiAscAsc()
	{
		$this->c->orderBy('string', Dibi::ASC);
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[0],
			$this->e[2],
			$this->e[1],
			$this->e[3],
		), $this->c->getResult());
	}

	public function testMultiDescAsc()
	{
		$this->c->orderBy('string', Dibi::DESC);
		$this->c->orderBy('int', Dibi::ASC);
		$this->assertSame(array(
			$this->e[1],
			$this->e[3],
			$this->e[0],
			$this->e[2],
		), $this->c->getResult());
	}

	public function testMultiAscDesc()
	{
		$this->c->orderBy('string', Dibi::ASC);
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[2],
			$this->e[0],
			$this->e[3],
			$this->e[1],
		), $this->c->getResult());
	}


	public function testMultiDescDesc()
	{
		$this->c->orderBy('string', Dibi::DESC);
		$this->c->orderBy('int', Dibi::DESC);
		$this->assertSame(array(
			$this->e[3],
			$this->e[1],
			$this->e[2],
			$this->e[0],
		), $this->c->getResult());
	}

}
