<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::setReadOnlyValue
 */
class ValueEntityFragment_setReadOnlyValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot write to an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->srov('unexists', 123);
	}

	public function test()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Property ValueEntityFragment_getset_Entity::$string is not read-only.');
		$this->e->srov('string', 'xyz');
	}

	public function testReadOnly1()
	{
		$this->assertSame(NULL, $this->e->readOnly);
		$this->assertSame($this->e, $this->e->srov('readOnly', 'xyz'));
		$this->assertSame('xyz', $this->e->readOnly);
	}

	public function testReadOnly2()
	{
		$this->assertSame($this->e, $this->e->srov('readOnly', 'xyz'));
		$this->assertSame('xyz', $this->e->readOnly);
		$this->assertSame($this->e, $this->e->srov('readOnly', 'abc'));
		$this->assertSame('abc', $this->e->readOnly);
	}

	public function testChanged()
	{
		$this->e->fireEvent('onPersist', new TestsRepository(new RepositoryContainer), 123);
		$this->assertFalse($this->e->isChanged('readOnly'));
		$this->e->srov('readOnly', 'xyz');
		$this->assertTrue($this->e->isChanged('readOnly'));
	}

	public function testProtected()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'setReadOnlyValue');
		$this->assertTrue($r->isProtected());
	}

	public function testFinal()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'setReadOnlyValue');
		$this->assertTrue($r->isFinal());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'setReadOnlyValue');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
