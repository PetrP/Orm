<?php

/**
 * @covers Orm\ManyToMany::ignore
 * @covers Orm\ManyToMany::handleCheckAndIgnore
 */
class ManyToMany_ignore_Test extends ManyToMany_Test
{

	public function testIgnore()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->ignore = true;
		$this->assertSame(NULL, $this->m2m->add(new OneToMany_Entity));
		$this->assertSame(0, count($this->m2m));
		$this->assertSame(NULL, $this->m2m->add(new OneToMany_Entity));
		$this->assertSame(0, count($this->m2m));
		$this->m2m->ignore = false;
		$this->m2m->add(new OneToMany_Entity);
		$this->assertSame(1, count($this->m2m));
		$this->m2m->add(new OneToMany_Entity);
		$this->assertSame(2, count($this->m2m));
		$this->m2m->ignore = true;
		$this->assertSame(NULL, $this->m2m->add(new OneToMany_Entity));
		$this->assertSame(2, count($this->m2m));
	}

	public function testDefault()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->set(array());

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->m2m->add($e));
		$this->assertSame(1, count($this->m2m));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'ignore');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
