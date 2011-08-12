<?php

use Orm\ManyToMany;

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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'isMappedByParent');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
