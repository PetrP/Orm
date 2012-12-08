<?php

use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaData::getEntityRules
 * @covers Orm\MetaData::createEntityRules
 */
class MetaData_getEntityRules_Both_Test extends TestCase
{
	private $m;
	private $m2;
	protected function setUp()
	{
		MetaData::clean();
		MetaData_Test_Entity::$metaData = NULL;
		$this->m = new RepositoryContainer;
		$this->m2 = new RepositoryContainer;
	}

	public function testMoreRepoCon()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m2);
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m2);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testNoRepoConCache()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MetaData_Test_Entity');
		MetaData::getEntityRules('MetaData_Test_Entity');
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity');
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testNoRepoConCache_OnlyFirst()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MetaData_Test_Entity');
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m2);
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testOnlyOnce1()
	{
		$a = MetaData::getEntityRules('EntityToArray_toArray_m1_Entity', $this->m);
		$b = MetaData::getEntityRules('EntityToArray_toArray_m1_Entity', $this->m2);
		$c = MetaData::getEntityRules('EntityToArray_toArray_m1_Entity');
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToOne', $a['e']['relationshipParam']);
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToOne', $b['e']['relationshipParam']);
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToOne', $c['e']['relationshipParam']);
		$this->assertSame($a['e']['relationshipParam'], $b['e']['relationshipParam']);
		$this->assertSame($c['e']['relationshipParam'], $b['e']['relationshipParam']);
		$this->assertSame($a, $b);
		$this->assertSame($c, $b);
	}

	public function testOnlyOnce2()
	{
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $this->m);
		$b = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $this->m2);
		$c = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity');
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $a['same1']['relationshipParam']);
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $b['same1']['relationshipParam']);
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $c['same1']['relationshipParam']);
		$this->assertSame($a['same1']['relationshipParam'], $b['same1']['relationshipParam']);
		$this->assertSame($c['same1']['relationshipParam'], $b['same1']['relationshipParam']);
		$this->assertSame($a, $b);
		$this->assertSame($c, $b);
	}

	public function testOnlyOnce3()
	{
		$m = new RepositoryContainer;
		$m->register('RelationshipMetaDataManyToMany_ManyToMany1_', 'TestsRepository');
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $this->m);
		try {
			MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);
			$this->fail();
		} catch (Exception $e) {}
		$b = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $this->m2);
		$c = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity');
		$this->assertSame($a, $b);
		$this->assertSame($c, $b);
	}

	public function testOnlyThisProperty()
	{
		$m = new RepositoryContainer;

		$origin = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $this->m);

		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'id');
		$this->assertSame(array('id'), array_keys($a));
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'same1');
		$this->assertSame(array('same1'), array_keys($a));
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'unexists');
		$this->assertSame(array(), array_keys($a));

		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);

		$this->assertSame($origin, $a);
	}

	public function testOnlyThisPropertyWithFail()
	{
		$m = new RepositoryContainer;
		$m->register('RelationshipMetaDataManyToMany_ManyToMany2_', 'TestsRepository'); // zpusobi ze metadata entity faulujou

		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'id');
		$this->assertSame(array('id'), array_keys($a));
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'same1');
		$this->assertSame(array('same1'), array_keys($a));
		$a = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m, 'unexists');
		$this->assertSame(array(), array_keys($a));

		try {
			MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);
			$this->fail();
		} catch (Exception $e1) {}

		try {
			MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);
			$this->fail();
		} catch (Exception $e2) {}

		try {
			MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);
			$this->fail();
		} catch (Exception $e3) {}

		try {
			MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $m);
			$this->fail();
		} catch (Exception $e4) {}

		$this->assertSame($e1->getMessage(), $e3->getMessage());
		$this->assertSame($e2->getMessage(), $e4->getMessage());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getEntityRules');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
