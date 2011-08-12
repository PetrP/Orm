<?php

/**
 * @covers Orm\DibiCollection::applyLimit
 */
class DibiCollection_applyLimit_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(10, 'limit', $this->c);
		$this->assertAttributeSame(20, 'offset', $this->c);
	}

	/**
	 * @covers Orm\DibiCollection::release
	 * @covers Orm\BaseDibiCollection::release
	 */
	public function testWipe()
	{
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DibiCollection_DibiCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(NULL, 'count', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->applyLimit(10, 20));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'applyLimit');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
