<?php

/**
 * @covers Orm\DataSourceCollection::process
 * @see DataSourceCollection_toString_applyLimit_Test
 * @see DataSourceCollection_toString_orderBy_Test
 * @see DataSourceCollection_toCollection_Test
 */
class DataSourceCollection_process_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->c->orderBy('xyz');
		$r = DataSourceCollection_DataSourceCollection::call($this->c, 'process');
		$this->assertSame(3, count($r));
		$this->assertSame(array(array('xyz', Dibi::ASC)), $r[0]);
		$this->assertSame(10, $r[1]);
		$this->assertSame(20, $r[2]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'process');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
