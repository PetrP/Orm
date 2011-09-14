<?php

/**
 * @covers Orm\ValueEntityFragment::__get
 * @see ValueEntityFragment_getter_Test
 */
class ValueEntityFragment_get_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->unexists;
	}

	public function testNoMetaMethod()
	{
		$this->assertSame(NULL, $this->e->method);
		$this->e->method = 3;
		$this->assertSame(3, $this->e->method);
	}

	public function testNotReadable()
	{
		$p = new ReflectionProperty('Orm\ValueEntityFragment', 'rules');
		setAccessible($p);
		$rules = $p->getValue($this->e);
		$rules['id']['get'] = NULL;
		$p->setValue($this->e, $rules);
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot read to a write-only property ValueEntityFragment_getset_Entity::$id.');
		$this->e->id;
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
