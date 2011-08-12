<?php

/**
 * @covers Orm\DibiCollection::fetchAssoc
 */
class DibiCollection_fetchAssoc_Test extends DibiCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(3);
		$this->assertSame(array(
			1 => 'boo',
			2 => 'foo',
			3 => 'bar',
		), $this->c->fetchAssoc('id=string'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'fetchAssoc');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
