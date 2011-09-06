<?php

use Orm\ManyToMany;
use Orm\RelationshipLoader;

/**
 * @covers Orm\ManyToMany::getWhereIsMapped
 */
class ManyToMany_getWhereIsMapped_Test extends ManyToMany_Test
{

	public function testHere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipLoader::MAPPED_HERE);
		$this->assertSame(RelationshipLoader::MAPPED_HERE, $this->m2m->getWhereIsMapped());
	}

	public function testThere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipLoader::MAPPED_BOTH);
		$this->assertSame(RelationshipLoader::MAPPED_BOTH, $this->m2m->getWhereIsMapped());
	}

	public function testBoth()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipLoader::MAPPED_THERE);
		$this->assertSame(RelationshipLoader::MAPPED_THERE, $this->m2m->getWhereIsMapped());
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
