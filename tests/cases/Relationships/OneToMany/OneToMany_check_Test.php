<?php

use Orm\BadEntityException;

/**
 * @covers Orm\OneToMany::check
 * @covers Orm\OneToMany::handleCheckAndIgnore
 */
class OneToMany_check_Test extends OneToMany_Test
{
	public function testDefault()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->o2m->add($e));
		$this->assertSame(1, count($this->o2m));
	}

	public function testTrue()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());
		$this->o2m->check = true;

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->o2m->add($e));
		$this->assertSame(1, count($this->o2m));
	}

	public function testFalse()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());
		$this->o2m->check = false;

		$e = new OneToMany_Entity;
		try {
			$this->o2m->add($e);
			$this->fail();
		} catch (BadEntityException $e) {
			$this->assertSame(0, count($this->o2m));
			$this->setExpectedException('Orm\BadEntityException', 'IgnoreOneToMany::check() OneToMany_Entity is not allowed for that relationship.');
			throw $e;
		}
	}

	public function testWithIgnore()
	{
		$this->o2m = new IgnoreOneToMany($this->e, $this->meta1);
		$this->o2m->set(array());
		$this->o2m->ignore = true;
		$this->o2m->check = false;

		$e = new OneToMany_Entity;
		$this->assertSame(NULL, $this->o2m->add($e));
		$this->assertSame(0, count($this->o2m));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'check');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
