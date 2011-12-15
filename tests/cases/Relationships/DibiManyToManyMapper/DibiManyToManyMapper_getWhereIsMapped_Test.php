<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\RelationshipMetaDataToMany;

/**
 * @covers Orm\DibiManyToManyMapper::getWhereIsMapped
 */
class DibiManyToManyMapper_getWhereIsMapped_Test extends TestCase
{

	public function test()
	{
		$m = new DibiManyToManyMapper_getWhereIsMapped_DibiManyToManyMapper(new DibiConnection(array('lazy' => true)));
		$m->parentParam = 'foo';
		$m->childParam = 'foo';
		$m->table = 'foo';
		$m->attach(new RelationshipMetaDataManyToMany('foo', 'foo', 'foo', 'foo', NULL, RelationshipMetaDataToMany::MAPPED_THERE));
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_THERE, $m->__getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'getWhereIsMapped');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
