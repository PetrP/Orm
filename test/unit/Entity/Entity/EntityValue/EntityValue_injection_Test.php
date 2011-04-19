<?php

require_once __DIR__ . '/../../../../boot.php';

class EntityValue_injection_Test extends TestCase
{
	/** @var EntityValue_injectionEntity */
	private $e;

	/** @var EntityValue_injectionRepository */
	private $r;

	protected function setUp()
	{
		$m = new Model;
		$this->e = new EntityValue_injectionEntity;
		$this->r = $m->entityValue_injection;
	}

	public function testNewRead()
	{
		$this->e->many;
		$this->assertSame(1, $this->e->many->create);
		$this->assertSame(1, $this->e->many->mapper->setValue);
		$this->assertSame(0, $this->e->many->setInjectedValue);
		$this->assertSame(0, $this->e->many->getInjectedValue);
		$this->assertSame(array(), $this->e->many->getInjectedValue());
		$this->e->many->persist();
		$this->assertSame(array(), $this->e->many->getInjectedValue());
	}

	public function testNewWrite()
	{
		$this->e->many = array(1,2,3);
		$this->assertSame(1, $this->e->many->create);
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
