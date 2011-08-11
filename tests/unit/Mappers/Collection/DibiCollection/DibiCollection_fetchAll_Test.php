<?php

/**
 * @covers Orm\DibiCollection::fetchAll
 */
class DibiCollection_fetchAll_Test extends DibiCollection_BaseConnected_Test
{

	public function testOk()
	{
		$this->e(3);
		$all = $this->c->fetchAll();
		$this->assertInternalType('array', $all);
		$this->assertSame(3, count($all));

		$this->assertInstanceOf('TestEntity', $all[0]);
		$this->assertSame(1, $all[0]->id);
		$this->assertSame('boo', $all[0]->string);

		$this->assertInstanceOf('TestEntity', $all[1]);
		$this->assertSame(2, $all[1]->id);
		$this->assertSame('foo', $all[1]->string);

		$this->assertInstanceOf('TestEntity', $all[2]);
		$this->assertSame(3, $all[2]->id);
		$this->assertSame('bar', $all[2]->string);
	}

	public function testNoRow()
	{
		$this->e(0);
		$all = $this->c->fetchAll();
		$this->assertSame(array(), $all);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'fetchAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
