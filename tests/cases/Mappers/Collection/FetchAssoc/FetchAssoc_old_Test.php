<?php

use Orm\FetchAssoc;
use Orm\RepositoryContainer;

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
		$this->setExpectedException('Orm\NotSupportedException', 'FetchAssoc "record" node (=) is not supported');
		FetchAssoc::apply($this->e, 'string,=,string');
	}

	public function testObjectNode()
	{
		$this->setExpectedException('Orm\NotSupportedException', 'FetchAssoc "object" node (@) is not supported');
		FetchAssoc::apply($this->e, 'string,@,string');
	}

	public function testUnknown()
	{
		$this->setExpectedException('Orm\MemberAccessException', "Cannot read an undeclared property ArrayCollection_Entity::\$unknown.");
		$r = FetchAssoc::apply($this->e, 'unknown,unknown');
	}

	public function testByEntityNull()
	{
		$r = FetchAssoc::apply($this->e, 'e,#');
		$this->assertSame(array(
			'' => array(
				$this->e[0],
				$this->e[1],
				$this->e[2],
				$this->e[3],
			),
		), $r);
	}

	public function testByEntity()
	{
		$m = new RepositoryContainer;
		$this->e[0]->e = $m->tests->getById(1);
		$this->e[1]->e = $m->tests->getById(1);
		$this->e[2]->e = $m->tests->getById(2);
		$this->e[3]->e = $m->tests->getById(2);
		$r = FetchAssoc::apply($this->e, 'e,#');
		$this->assertSame(array(
			1 => array(
				$this->e[0],
				$this->e[1],
			),
			2 => array(
				$this->e[2],
				$this->e[3],
			),
		), $r);
	}

}
