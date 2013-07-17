<?php

/**
 * @covers Orm\DibiResultWrapper::getIterator
 */
class DibiResultWrapper_getIterator_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertInstanceOf('Orm\DibiResultWrapperIterator', $this->w->getIterator());
		$this->assertAttributeSame($this->w, 'result', $this->w->getIterator());
	}

	public function test2()
	{
		$this->assertNotSame($this->w->getIterator(), $this->w->getIterator());
	}

	public function test3()
	{
		$this->assertSame($this->w->count(), $this->w->getIterator()->count());
		$this->assertSame($this->w->toArray(), iterator_to_array($this->w->getIterator()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'getIterator');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
