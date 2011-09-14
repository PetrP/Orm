<?php

use Orm\DataSourceCollection;

/**
 * @covers Orm\DataSourceCollection::__call
 * @covers Orm\FindByHelper::parse
 */
class DataSourceCollection_call_findBy_Test extends DataSourceCollection_Base_Test
{

	private function t(DataSourceCollection $cNew, DataSourceCollection $cOld, array $findBy)
	{
		$findBy = array_merge($this->readAttribute($cOld, 'findBy'), $findBy);
		$this->assertNotSame($cOld, $cNew);
		$this->assertAttributeSame($findBy, 'findBy', $cNew);
	}

	public function test()
	{
		$c = $this->c->findByName('abc');
		$this->t($c, $this->c, array(array('name' => 'abc')));

		$this->t($c->findByName('cba'), $this->c, array(array('name' => 'abc'), array('name' => 'cba')));
	}

	public function testCaseInsensitive()
	{
		$this->t($this->c->fIndbyName('abc'), $this->c, array(array('name' => 'abc')));
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method Orm\DataSourceCollection::findXyz()');
		$this->c->findXyz('abc');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', '__call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
