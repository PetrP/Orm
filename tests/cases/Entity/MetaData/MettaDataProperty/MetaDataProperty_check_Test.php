<?php

use Orm\MetaData;
use Orm\MetaDataProperty;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaDataProperty::check
 */
class MetaDataProperty_check_Test extends TestCase
{
	public function test()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$p = $m->addProperty('id', '');
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\MetaDataProperty::check() is deprecated.');
		$p->check(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
