<?php

/**
 * @covers Orm\DibiCollection::toCollection
 * @covers Orm\DibiCollection::process
 */
class DibiCollection_toCollection_Test extends DibiCollection_Base_Test
{

	public function test1()
	{
		$c = $this->c->toCollection();
		$this->assertInstanceOf('Orm\DibiCollection', $c);
		$this->assertSame('Orm\DibiCollection', get_class($c));
		$this->assertNotSame($this->c, $c);
	}

	public function test2()
	{
		$c = $this->c->toCollection();
		$this->assertAttributeSame($this->readAttribute($this->c, 'repository'), 'repository', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'connection'), 'connection', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'tableName'), 'tableName', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'result'), 'result', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'count'), 'count', $c);
	}

	public function test3()
	{
		$this->c->applyLimit(10, 11);
		$this->c->orderBy('xxx');
		$c = $this->c->toCollection();

		$this->assertAttributeSame(NULL, 'offset', $c);
		$this->assertAttributeSame(NULL, 'limit', $c);
		$this->assertAttributeSame(array(), 'sorting', $c);

		$this->assertAttributeSame(11, 'sourceOffset', $c);
		$this->assertAttributeSame(10, 'sourceLimit', $c);
		$this->assertAttributeSame(array(array('e.xxx', Dibi::ASC)), 'sourceSorting', $c);
	}

	public function test4()
	{
		$c = $this->c->toCollection()->findByXxx('aaa');
		$c->where('1=1');
		$c = $c->toCollection();

		$this->assertAttributeSame(array(array('1=1')), 'where', $c);
		$this->assertAttributeSame(array(array('xxx' => 'aaa')), 'findBy', $c);
	}

	public function testSubClass()
	{
		$cOrigin = new DibiCollection_DibiCollection('table', $this->m->connection, $this->r);
		$c = $cOrigin->toCollection();
		$this->assertInstanceOf('Orm\DibiCollection', $c);
		$this->assertSame('DibiCollection_DibiCollection', get_class($c));
		$this->assertNotSame($cOrigin, $c);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'toCollection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
