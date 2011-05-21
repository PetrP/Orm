<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::orderBy
 */
class ArrayCollection_orderBy_Test extends ArrayCollection_Base_Test
{

	public function testResetResult()
	{
		ArrayCollection_ArrayCollection::set($this->c, 'result', array());
		ArrayCollection_ArrayCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
	}

	public function testBase()
	{
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(array(array('xxx', Dibi::ASC)), 'sorting', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->orderBy('xxx'));
	}

	public function testMore()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('yyy');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('yyy', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testMoreSame()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testAsc()
	{
		$this->c->orderBy('xxx', Dibi::ASC);
		$this->c->orderBy('xxx', 'ASC');
		$this->c->orderBy('xxx', 'asc');
		$this->c->orderBy('xxx', 'aSc');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
			array('xxx', Dibi::ASC),
		), 'sorting', $this->c);
	}

	public function testDesc()
	{
		$this->c->orderBy('xxx', Dibi::DESC);
		$this->c->orderBy('xxx', 'DESC');
		$this->c->orderBy('xxx', 'desc');
		$this->c->orderBy('xxx', 'dEsC');
		$this->assertAttributeSame(array(
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
			array('xxx', Dibi::DESC),
		), 'sorting', $this->c);
	}

	public function testBad()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "Orm\\ArrayCollection::orderBy() Direction excepted Dibi::ASC or Dibi::DESC, 'bad' given");
		$this->c->orderBy('xxx', 'bad');
	}

	public function testWipe()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->assertAttributeNotEmpty('sorting', $this->c);
		$this->c->orderBy(array());
		$this->assertAttributeEmpty('sorting', $this->c);
	}

	public function testArray()
	{
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->c->orderBy('xxx');
		$this->c->orderBy(array('aaA' => Dibi::ASC, 'bBb' => Dibi::DESC));
		$this->assertAttributeSame(array(
			array('aaA', Dibi::ASC),
			array('bBb', Dibi::DESC),
		), 'sorting', $this->c);
	}
}
