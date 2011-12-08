<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class ValueEntityFragment_injection_Test extends TestCase
{
	/** @var ValueEntityFragment_injectionEntity */
	private $e;

	/** @var ValueEntityFragment_injectionRepository */
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new ValueEntityFragment_injectionEntity;
		$this->r = $m->valueEntityFragment_injection;
	}

	public function testNewRead()
	{
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(NULL, $this->e->many->mapper);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(NULL, $this->e->many->getInjectedValue());
		$this->setExpectedException('Orm\EntityNotAttachedException', 'ValueEntityFragment_injectionEntity is not attached to repository.');
		$this->e->many->persist();
	}

	public function testNewWrite()
	{
		$this->setExpectedException('Orm\EntityNotAttachedException', 'ValueEntityFragment_injectionEntity is not attached to repository.');
		$this->e->many = array(1,2,3);
	}

	public function testNewReadAttach()
	{
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(NULL, $this->e->many->mapper);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(NULL, $this->e->many->getInjectedValue());
		$this->r->attach($this->e);
		$this->e->many->persist();
		$this->assertInstanceOf('ValueEntityFragment_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(array(), $this->e->many->getInjectedValue());
	}

	public function testNewWriteAttach()
	{
		$this->r->attach($this->e);
		$this->e->many = array(1,2,3);
		$this->assertSame(1, $this->e->many->create);
		$this->assertInstanceOf('ValueEntityFragment_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(1, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
	}

	public function testPersistedRead()
	{
		$this->e->fireEvent('onLoad', $this->r, array(
			'many' => serialize(array(1=>1,2=>2,3=>3)),
			'id' => 1,
		));
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
	}

	public function testPersistedWrite()
	{
		$this->e->fireEvent('onLoad', $this->r, array(
			'many' => serialize(array(1=>1,2=>2,3=>3)),
			'id' => 1,
		));
		$this->e->many = array(1,2,4);
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(1, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(1=>1,2=>2,4=>4), $this->e->many->getInjectedValue());
	}

	public function testPersistedMultiWrite()
	{
		$this->e->fireEvent('onLoad', $this->r, array(
			'many' => serialize(array(1=>1,2=>2,3=>3)),
			'id' => 1,
		));
		$this->e->many = array(1,2,4);
		$this->e->many = array(4);
		$this->e->many = array(3);
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(3, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(3=>3), $this->e->many->getInjectedValue());
	}

	public function testBad()
	{
		$e = new ValueEntityFragment_injectionBadEntity;
		$this->setExpectedException('Orm\BadReturnException', "ValueEntityFragment_injectionBadEntity::createInjection() must return Orm\\IEntityInjection, 'Directory' given.");
		$e->i;
	}
}
