<?php

use Orm\MetaData;
use Orm\MetaDataProperty;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaDataProperty::setOneToOne
 * @covers Orm\MetaDataProperty::check
 */
class MetaDataProperty_setOneToOne_Test extends TestCase
{
	private $model;
	private $m;

	protected function setUp()
	{
		$this->model = new RepositoryContainer;
		$this->m = new MetaData('MetaData_Test_Entity');
	}

	private function get(MetaDataProperty $p, $key = 'relationship')
	{
		$a = $p->toArray();
		return $a[$key];
	}

	public function testBase()
	{
		$p = $this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('MetaData_Test2')
		;
		$this->get($p, 'relationshipParam')->check($this->model);

		$this->assertSame(MetaData::OneToOne, $this->get($p));
		$this->assertSame('MetaData_Test2', (string) $this->get($p, 'relationshipParam'));
		$rp = $this->get($p, 'relationshipParam');
		$this->assertInstanceOf('Orm\RelationshipMetaDataOneToOne', $rp);
		$this->assertSame(Orm\MetaData::OneToOne, $rp->type);
		$this->assertSame('MetaData_Test2', $rp->childRepository);
		$this->assertSame(NULL, $rp->childParam);
		$this->assertSame('MetaData_Test_Entity', $rp->parentEntityName);
		$this->assertSame('id', $rp->parentParam);
	}

	public function testBaseWithParam()
	{
		$p = $this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('MetaData_Test2', 'abc')
		;

		$this->assertSame(MetaData::OneToOne, $this->get($p));
		$this->assertSame('MetaData_Test2', (string) $this->get($p, 'relationshipParam'));
		$rp = $this->get($p, 'relationshipParam');
		$this->assertInstanceOf('Orm\RelationshipMetaDataOneToOne', $rp);
		$this->assertSame(Orm\MetaData::OneToOne, $rp->type);
		$this->assertSame('MetaData_Test2', $rp->childRepository);
		$this->assertSame('abc', $rp->childParam);
		$this->assertSame('MetaData_Test_Entity', $rp->parentEntityName);
		$this->assertSame('id', $rp->parentParam);
	}

	public function testTwice()
	{
		$this->setExpectedException('Orm\MetaDataException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('MetaData_Test2')
			->setOneToOne('MetaData_Test2')
		;
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\MetaDataException', 'MetaData_Test_Entity::$id {1:1} You must specify foreign repository {1:1 repositoryName}');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('')
		;
	}

	public function testBadRepo()
	{
		$p = $this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('BlaBlaBla')
		;
		$this->setExpectedException('Orm\MetaDataException', 'BlaBlaBla isn\'t repository in MetaData_Test_Entity::$id');
		$this->get($p, 'relationshipParam')->check($this->model);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setOneToOne');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
