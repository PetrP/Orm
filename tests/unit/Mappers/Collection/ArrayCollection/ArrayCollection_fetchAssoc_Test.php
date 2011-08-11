<?php

/**
 * @covers Orm\ArrayCollection::fetchAssoc
 */
class ArrayCollection_fetchAssoc_Test extends ArrayCollection_Base_Test
{

	public function test()
	{
		$a = $this->c->fetchAssoc('string|int');
		$this->assertSame(array(
			'a' => array(2 => $this->e[0], 3 => $this->e[2]),
			'b' => array(1 => $this->e[1], 4 => $this->e[3]),
		), $a);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'fetchAssoc');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
