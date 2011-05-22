<?php

use Orm\FetchAssoc;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\FetchAssoc::apply
 */
class FetchAssoc_apply_Test extends FetchAssoc_Base_Test
{

	public function test1()
	{
		$r = FetchAssoc::apply($this->e, 'string[]string|string');
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

	public function test2()
	{
		$r = FetchAssoc::apply($this->e, 'string|string=string');
		$this->assertSame(array(
			'a' => array('a' => 'a'),
			'b' => array('b' => 'b'),
		), $r);
	}

	public function testEmptyRows()
	{
		$r = FetchAssoc::apply(array(), 'string');
		$this->assertSame(array(), $r);
	}

	public function testEmptyAssoc()
	{
		$r = FetchAssoc::apply($this->e, '');
		$this->assertSame($this->e, $r);
	}

	public function testObjectNode()
	{
		$this->setExpectedException('Nette\NotSupportedException', 'FetchAssoc "object" node (->) is not supported');
		FetchAssoc::apply($this->e, '->string');
	}

	public function testUnknown()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "Unknown column 'unknown' in associative descriptor.");
		$r = FetchAssoc::apply($this->e, 'unknown');
	}

}
