<?php

use Orm\FetchAssoc;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\FetchAssoc::apply
 * @covers Orm\FetchAssoc::oldFetchAssoc
 */
class FetchAssoc_old_Test extends FetchAssoc_Base_Test
{

	public function test1()
	{
		$r = FetchAssoc::apply($this->e, 'string,#,string,string');
		$this->assertSame(array(
			'a' => array(
				array('a' => array('a' => $this->e[0])),
				array('a' => array('a' => $this->e[2])),
			),
			'b' => array(
				array('b' => array('b' => $this->e[1])),
				array('b' => array('b' => $this->e[3])),
			),
		), $r);
	}

	public function testEmptyRows()
	{
		$r = FetchAssoc::apply(array(), 'string,string');
		$this->assertSame(array(), $r);
	}

	public function testArrayNode()
	{
		$this->setExpectedException('Nette\NotSupportedException', 'FetchAssoc "record" node (=) is not supported');
		FetchAssoc::apply($this->e, 'string,=,string');
	}

	public function testObjectNode()
	{
		$this->setExpectedException('Nette\NotSupportedException', 'FetchAssoc "object" node (@) is not supported');
		FetchAssoc::apply($this->e, 'string,@,string');
	}

	public function testUnknown()
	{
		$this->setExpectedException('Nette\MemberAccessException', "Cannot read an undeclared property ArrayCollection_Entity::\$unknown.");
		$r = FetchAssoc::apply($this->e, 'unknown,unknown');
	}

}
