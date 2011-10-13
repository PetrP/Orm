<?php

use Orm\NotValidException;
use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::getValue
 * @see ValueEntityFragment_getter_Test
 */
class ValueEntityFragment_getValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot read an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->gv('unexists');
	}

	public function test()
	{
		$this->e->string = 'xyz';
		$this->assertSame('xyz', $this->e->gv('string'));
	}

	public function testNotReadable()
	{
		$p = new ReflectionProperty('Orm\ValueEntityFragment', 'rules');
		setAccessible($p);
		$rules = $p->getValue($this->e);
		$rules['id']['get'] = NULL;
		$p->setValue($this->e, $rules);
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot read to a write-only property ValueEntityFragment_getset_Entity::$id.');
		$this->e->fireEvent('onPersist', new TestsRepository(new RepositoryContainer), 2);
	}

	public function testRestoreChanged()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$e->fireEvent('onPersist', new TestsRepository(new RepositoryContainer), 3);
		$this->assertFalse($e->isChanged());
		try {
			$e->foo;
			$this->fail();
		} catch (UnexpectedValueException $ex) {
			throw $ex;
		} catch (Exception $ex) {}

		$this->assertFalse($e->isChanged());
	}

	public function testRestoreChangedInvalidValueNeed()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$e->fireEvent('onLoad', new TestsRepository(new RepositoryContainer), array('id' => 3, 'foo2' => 'xyz'));
		$e->fireEvent('onPersist', new TestsRepository(new RepositoryContainer), 3);
		$this->assertFalse($e->isChanged());
		try {
			$e->__getValue('foo2', true);
			$this->fail();
		} catch (NotValidException $ex) {}

		$this->assertFalse($e->isChanged());
	}

	public function testRestoreChangedInvalidValueNotNeed()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$e->fireEvent('onLoad', new TestsRepository(new RepositoryContainer), array('id' => 3, 'foo2' => 'xyz'));
		$e->fireEvent('onPersist', new TestsRepository(new RepositoryContainer), 3);
		$this->assertFalse($e->isChanged());
		$v = $e->__getValue('foo2', false);
		$this->assertSame(NULL, $v);

		$this->assertFalse($e->isChanged());
	}

	public function testLazy()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$r = new ValueEntityFragment_getValue_LazyRepository(new RepositoryContainer);
		$e->fireEvent('onAttach', $r);
		$e->foo2 = 4;
		$this->assertSame(4, $e->foo2);
		$this->assertSame(0, $r->count);
		$this->assertSame('lazy', $e->mixed);
		$this->assertSame(1, $r->count);
		$this->assertSame(5, $e->foo3);
		$this->assertSame(4, $e->foo2);
		$this->assertFalse(isset($e->unexists));
		$this->assertSame(1, $r->count);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'getValue');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
