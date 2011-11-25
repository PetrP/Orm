<?php

/**
 * @covers Orm\OneToMany::ignore
 * @covers Orm\OneToMany::handleCheckAndIgnore
 */
class OneToMany_ignore_Test extends OneToMany_Test
{

	public function testIgnore()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());
		$this->o2m->ignore = true;
		$this->assertSame(NULL, $this->o2m->add(new OneToMany_Entity));
		$this->assertSame(0, count($this->o2m));
		$this->assertSame(NULL, $this->o2m->add(new OneToMany_Entity));
		$this->assertSame(0, count($this->o2m));
		$this->o2m->ignore = false;
		$this->o2m->add(new OneToMany_Entity);
		$this->assertSame(1, count($this->o2m));
		$this->o2m->add(new OneToMany_Entity);
		$this->assertSame(2, count($this->o2m));
		$this->o2m->ignore = true;
		$this->assertSame(NULL, $this->o2m->add(new OneToMany_Entity));
		$this->assertSame(2, count($this->o2m));
	}

	public function testDefault()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->o2m->add($e));
		$this->assertSame(1, count($this->o2m));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'ignore');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
