<?php

use Orm\ManyToMany;
use Orm\RelationshipMetaDataToMany;

/**
 * @covers Orm\ManyToMany::isMappedByParent
 */
class ManyToMany_isMappedByParent_Test extends ManyToMany_Test
{

	public function testIs()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', true);
		$this->assertTrue($this->m2m->isMappedByParent());
	}

	public function testNot()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', false);
		$this->assertFalse($this->m2m->isMappedByParent());
	}

	public function testHere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_HERE);
		$this->assertTrue($this->m2m->isMappedByParent());
	}

	public function testThere()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_THERE);
		$this->assertFalse($this->m2m->isMappedByParent());
	}

	public function testBoth()
	{
		$this->m2m = new ManyToMany($this->e, get_class($this->r), 'param', 'param', RelationshipMetaDataToMany::MAPPED_BOTH);
		$this->assertTrue($this->m2m->isMappedByParent());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'isMappedByParent');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
