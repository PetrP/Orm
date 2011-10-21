<?php

use Orm\RepositoryContainer;
use Orm\Callback;
use Orm\SqlConventional;
use Orm\NoConventional;

/**
 * @covers Orm\ArrayMapper::flush
 */
class ArrayMapper_flush_pesist_Test extends TestCase
{
	private $r;
	private $m;
	private $e;
	protected function setUp()
	{
		$this->r = new ArrayMapper_flush_Repository(new RepositoryContainer);
		$this->m = $this->r->mapper;
		$this->e = new ArrayMapper_flush_Entity;
	}

	private function t($v)
	{
		$this->m->persist($this->e);
		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		$this->assertSame($v, $storage[3]['mixed']);
	}

	public function testScalarString()
	{
		$this->e->mixed = 'string';
		$this->t('string');
	}

	public function testScalarInt()
	{
		$this->e->mixed = 123;
		$this->t(123);
	}

	public function testScalarBool()
	{
		$this->e->mixed = true;
		$this->t(true);
	}

	public function testNull()
	{
		$this->e->mixed = NULL;
		$this->t(NULL);
	}

	public function testDate()
	{
		$this->e->mixed = new DateTime('2011-11-11');
		$this->t('2011-11-11T00:00:00+01:00');
	}

	public function testArray()
	{
		$this->e->mixed = array('cow', 'boy');
		$this->t(array('cow', 'boy'));
	}

	public function testArrayObject()
	{
		$this->e->mixed = $a = new ArrayObject(array('cow', 'boy'));
		$this->t($a);
	}

	public function testArrayObjectSubClass()
	{
		$this->e->mixed = new MyArrayObject(array('cow', 'boy'));
		$this->setExpectedException('Orm\MapperPersistenceException', "ArrayMapper_flush_Mapper: can't persist ArrayMapper_flush_Entity::\$mixed; it contains 'MyArrayObject'.");
		$this->t(NULL);
	}

	public function testToString()
	{
		$this->e->mixed = Callback::create($this, 'testToString');
		$this->t('ArrayMapper_flush_pesist_Test::testToString');
	}

	public function testEntity()
	{
		$this->e->mixed = $this->r->getById(2);
		$this->t(2);
	}

	public function testInjection()
	{
		$this->e->mixed = new ArrayMapper_flush_Injection;
		$this->e->mixed->setInjectedValue(array('foo', 'bar'));
		$this->t(array('foo', 'bar'));
	}

	public function testBad()
	{
		$this->e->mixed = new ArrayIterator(array());
		$this->setExpectedException('Orm\MapperPersistenceException', "ArrayMapper_flush_Mapper: can't persist ArrayMapper_flush_Entity::\$mixed; it contains 'ArrayIterator'.");
		$this->t(NULL);
	}

	public function testConventional1()
	{
		$this->m->conv = new NoConventional($this->m);
		$this->e->miXed = 'string';
		$this->m->persist($this->e);
		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		unset($storage[3]['date']);
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'mixed' => NULL,
			'miXed' => 'string',
		), $storage[3]);
	}

	public function testConventional2()
	{
		$this->m->conv = new SqlConventional($this->m);
		$this->e->miXed = 'string';
		$this->m->persist($this->e);
		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		unset($storage[3]['date']);
		$this->assertSame(array(
			'id' => NULL, // protoze se nepersistuje pres repository
			'string' => '',
			'mixed' => NULL,
			'mi_xed' => 'string',
		), $storage[3]);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'flush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
