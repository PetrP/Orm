<?php

use Orm\BadEntityException;

/**
 * @covers Orm\ManyToMany::check
 * @covers Orm\ManyToMany::handleCheckAndIgnore
 */
class ManyToMany_check_Test extends ManyToMany_Test
{
	public function testDefault()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->set(array());

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->m2m->add($e));
		$this->assertSame(1, count($this->m2m));
	}

	public function testTrue()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->set(array());
		$this->m2m->check = true;

		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->m2m->add($e));
		$this->assertSame(1, count($this->m2m));
	}

	public function testFalse()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->set(array());
		$this->m2m->check = false;

		$e = new OneToMany_Entity;
		try {
			$this->m2m->add($e);
			$this->fail();
		} catch (BadEntityException $e) {
			$this->assertSame(0, count($this->m2m));
			$this->setExpectedException('Orm\BadEntityException', 'IgnoreManyToMany::check() OneToMany_Entity is not allowed for that relationship.');
			throw $e;
		}
	}

	public function testWithIgnore()
	{
		$this->m2m = new IgnoreManyToMany($this->e, $this->meta1);
		$this->m2m->set(array());
		$this->m2m->ignore = true;
		$this->m2m->check = false;

		$e = new OneToMany_Entity;
		$this->assertSame(NULL, $this->m2m->add($e));
		$this->assertSame(0, count($this->m2m));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'check');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
