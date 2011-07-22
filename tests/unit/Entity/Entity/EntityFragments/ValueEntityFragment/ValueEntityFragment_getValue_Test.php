<?php

use Nette\UnexpectedValueException;
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
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
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
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read to a write-only property ValueEntityFragment_getset_Entity::$id.');
		$this->e->___event($this->e, 'persist', new TestsRepository(new RepositoryContainer), 2);
	}

	public function testRestoreChanged()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$e->___event($e, 'persist', new TestsRepository(new RepositoryContainer), 3);
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
		$e->___event($e, 'load', new TestsRepository(new RepositoryContainer), array('id' => 3, 'foo2' => 'xyz'));
		$e->___event($e, 'persist', new TestsRepository(new RepositoryContainer), 3);
		$this->assertFalse($e->isChanged());
		try {
			$e->__getValue('foo2', true);
			$this->fail();
		} catch (UnexpectedValueException $ex) {}

		$this->assertFalse($e->isChanged());
	}

	public function testRestoreChangedInvalidValueNotNeed()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$e->___event($e, 'load', new TestsRepository(new RepositoryContainer), array('id' => 3, 'foo2' => 'xyz'));
		$e->___event($e, 'persist', new TestsRepository(new RepositoryContainer), 3);
		$this->assertFalse($e->isChanged());
		$v = $e->__getValue('foo2', false);
		$this->assertSame(NULL, $v);

		$this->assertFalse($e->isChanged());
	}

	public function testLazy()
	{
		$e = new ValueEntityFragment_getValue_Entity;
		$r = new ValueEntityFragment_getValue_LazyRepository(new RepositoryContainer);
		$e->___event($e, 'attach', $r);
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

}
