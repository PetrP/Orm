<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::setValueHelper
 */
class EntityValue_injection_Test extends TestCase
{
	/** @var EntityValue_injectionEntity */
	private $e;

	/** @var EntityValue_injectionRepository */
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new EntityValue_injectionEntity;
		$this->r = $m->entityValue_injection;
	}

	public function testNewRead()
	{
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame('ArrayManyToManyMapper', get_class($this->e->many->mapper));
		$this->assertNotInstanceOf('EntityValue_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(NULL, $this->e->many->getInjectedValue());
		$this->setExpectedException('InvalidStateException', 'EntityValue_injectionEntity is not attached to repository.');
		$this->e->many->persist();
	}

	public function testNewWrite()
	{
		$this->setExpectedException('InvalidStateException', 'EntityValue_injectionEntity is not attached to repository.');
		$this->e->many = array(1,2,3);
	}

	public function testNewReadAttach()
	{
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame('ArrayManyToManyMapper', get_class($this->e->many->mapper));
		$this->assertNotInstanceOf('EntityValue_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(NULL, $this->e->many->getInjectedValue());
		$this->r->attach($this->e);
		$this->e->many->persist();
		$this->assertInstanceOf('EntityValue_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(array(), $this->e->many->getInjectedValue());
	}

	public function testNewWriteAttach()
	{
		$this->r->attach($this->e);
		$this->e->many = array(1,2,3);
		$this->assertSame(1, $this->e->many->create);
		$this->assertInstanceOf('EntityValue_injection_ManyToManyMapper', $this->e->many->mapper);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(1, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(1=>1,2=>2,3=>3), $this->e->many->getInjectedValue());
	}

	public function testPersistedRead()
	{
		$this->e->___event($this->e, 'load', $this->r, array(
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
		$this->e->___event($this->e, 'load', $this->r, array(
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
		$this->e->___event($this->e, 'load', $this->r, array(
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
}
