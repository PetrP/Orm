<?php

/**
 * @covers Orm\DibiCollection::getIterator
 */
class DibiCollection_getIterator_Test extends DibiCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(3);
		$i = $this->c->getIterator();
		$this->assertInstanceOf('Orm\HydrateEntityIterator', $i);
		$a = iterator_to_array($i);
		$this->assertSame(3, count($a));
		$this->assertInstanceOf('TestEntity', $a[0]);
	}

	public function testNoRow()
	{
		$this->e(0);
		$this->assertSame(array(), iterator_to_array($this->c->getIterator()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getIterator');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
