<?php

use Orm\DibiCollection;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::__call
 * @covers Orm\FindByHelper::parse
 */
class DibiCollection_call_findBy_Test extends DibiCollection_Base_Test
{

	private function t(DibiCollection $cNew, DibiCollection $cOld, array $findBy)
	{
		$findBy = array_merge($this->readAttribute($cOld, 'findBy'), $findBy);
		$this->assertNotSame($cOld, $cNew);
		$this->assertAttributeSame($findBy, 'findBy', $cNew);
	}

	public function test()
	{
		$c = $this->c->findByName('abc');
		$this->t($c, $this->c, array(array('name' => 'abc')));

		$this->t($c->findByName('cba'), $this->c, array(array('name' => 'abc'), array('name' => 'cba')));
	}

	public function testCaseInsensitive()
	{
		$this->t($this->c->fIndbyName('abc'), $this->c, array(array('name' => 'abc')));
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Call to undefined method Orm\DibiCollection::findXyz()');
		$this->c->findXyz('abc');
	}

}
