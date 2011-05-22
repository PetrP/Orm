<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::__call
 * @covers Orm\FindByHelper::parse
 */
class ArrayCollection_call_findBy_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->findByString('a');
		$this->assertSame(array('a', 'a'), $c->fetchPairs(NULL, 'string'));
		$this->assertSame(array('a', 'b', 'a', 'b'), $this->c->fetchPairs(NULL, 'string'));
	}

	public function testCaseInsensitive()
	{
		$c = $this->c->fIndbyString('abc');
		$this->assertSame(array(), $c->fetchAll());
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Call to undefined method Orm\ArrayCollection::findXyz()');
		$this->c->findXyz('abc');
	}

}
