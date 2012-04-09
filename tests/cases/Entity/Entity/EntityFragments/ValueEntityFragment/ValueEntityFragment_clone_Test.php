<?php

use Orm\RepositoryContainer;
use Orm\OneToMany;
use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\ValueEntityFragment::__clone
 */
class ValueEntityFragment_clone_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = $m->testentityrepository->getById(1);
	}

	public function testBase()
	{
		$e = $this->e;

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());

		$ee = clone $e;

		$this->assertSame(NULL, isset($ee->id) ? $ee->id : NULL);
		$this->assertSame('string', $ee->string);
		$this->assertSame('2011-11-11', $ee->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(true, $ee->isChanged());

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('TestEntityRepository', get_class($e->repository));
		$this->assertSame(false, $e->isChanged());
	}

	public function testChange()
	{
		$e = $this->e;
		$ee = clone $e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$ee->date = '2010-10-10';
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('2010-10-10', $ee->date->format('Y-m-d'));
	}

	public function testChangeObject()
	{
		$e = $this->e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));

		$ee = clone $e;

		$ee->date->modify('-50 years');
		$this->assertSame('1961-11-11', $ee->date->format('Y-m-d'));
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
	}

	public function testEntity()
	{
		$e = new ValueEntityFragment_clone_Entity;
		$object = new TestEntity;

		$e->mixed = $object;

		$this->assertSame($object, $e->mixed);

		$ee = clone $e;

		$this->assertSame($object, $e->mixed);
		$this->assertSame($object, $ee->mixed);
	}

	public function testObject()
	{
		$e = new ValueEntityFragment_clone_Entity;
		$object = (object) array('aa' => 'bb');

		$e->mixed = $object;

		$this->assertSame($object, $e->mixed);

		$ee = clone $e;

		$this->assertSame($object, $e->mixed);
		$this->assertNotSame($object, $ee->mixed);
		$this->assertEquals($object, $ee->mixed);
	}

	public function testRelationship()
	{
		$e = new ValueEntityFragment_clone_Entity;
		$object = new OneToMany($e, new RelationshipMetaDataOneToMany('ValueEntityFragment_clone_Entity', 'mixed', 'TestEntityRepository', ''));

		$e->mixed = $object;

		$this->assertSame($object, $e->mixed);

		$ee = clone $e;

		$this->assertSame($object, $e->mixed);
		$this->assertNotSame($object, $ee->mixed);
		$this->assertSame(array(), $ee->mixed); // entita v pripade ze je nastavena klasicka asociace vytvori novou
	}

	public function testInjection()
	{
		$e = new ValueEntityFragment_clone_Entity;
		$object = new ValueEntityFragment_clone_Injection;
		$object->setInjectedValue('abc');

		$e->mixed = $object;

		$this->assertSame($object, $e->mixed);

		$ee = clone $e;

		$this->assertSame($object, $e->mixed);
		$this->assertNotSame($object, $ee->mixed);
		$this->assertSame('abc', $ee->mixed); // entita v pripade ze je nastavena injekce vytvori novou
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__clone');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
