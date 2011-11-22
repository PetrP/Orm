<?php

use Orm\ManyToMany;
use Orm\RelationshipMetaDataToMany;

/**
 * @covers Orm\ManyToMany::getWhereIsMapped
 */
class ManyToMany_getWhereIsMapped_Test extends ManyToMany_Test
{

	public function testHere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_HERE);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_HERE, $this->m2m->getWhereIsMapped());
	}

	public function testThere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_BOTH);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_BOTH, $this->m2m->getWhereIsMapped());
	}

	public function testBoth()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_THERE);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_THERE, $this->m2m->getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getWhereIsMapped');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
