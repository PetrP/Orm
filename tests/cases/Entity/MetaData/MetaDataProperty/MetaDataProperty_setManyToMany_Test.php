<?php

use Orm\MetaData;
use Orm\OldManyToMany;
use Orm\MetaDataProperty;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaDataProperty::setManyToMany
 * @covers Orm\MetaDataProperty::setToMany
 * @covers Orm\RelationshipLoader::__construct
 * @covers Orm\RelationshipLoader::check
 */
class MetaDataProperty_setManyToMany_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new MetaData('MetaData_Test_Entity');
	}

	private function get(MetaDataProperty $p, $key = 'relationship')
	{
		$a = $p->toArray();
		return $a[$key];
	}

	private function t(MetaDataProperty $p, $class, $name)
	{
		$this->assertSame(MetaData::ManyToMany, $this->get($p));

		$i = $this->get($p, 'injection');
		$this->assertInstanceOf('Orm\Callback', $i);

		$ii = $i->getNative();
		$this->assertInstanceOf('Orm\InjectionFactory', $ii[0]);
		$this->assertAttributeSame($class, 'className', $ii[0]);

		$ii = $this->readAttribute($ii[0], 'callback');
		$this->assertInstanceOf('Orm\RelationshipLoader', $ii[0]);
		$this->assertSame('create', $ii[1]);

		$this->assertAttributeSame($class, 'class', $ii[0]);
	}

	public function testBase()
	{
		$p = $this->m->addProperty('id', 'MetaDataProperty_setManyToMany_ManyToMany')
			->setManyToMany()
		;
		$this->t($p, 'MetaDataProperty_setManyToMany_ManyToMany', 'MetaDataProperty_setManyToMany_ManyToMany');
	}

	public function testTwice()
	{
		$this->setExpectedException('Orm\MetaDataException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaDataProperty_setManyToMany_ManyToMany')
			->setManyToMany()
			->setManyToMany()
		;
	}

	public function testMultipleType()
	{
		$this->setExpectedException('Orm\MetaDataException', 'MetaData_Test_Entity::$id {m:m} excepts Orm\ManyToMany class as type, \'string|int\' given');
		$this->m->addProperty('id', 'string|int')
			->setManyToMany()
		;
	}

	public function testBadType_unexist()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'MetaData_Test_Entity::$id {m:m} excepts Orm\ManyToMany class as type, class \'BadClass\' doesn\'t exists');
		$this->m->addProperty('id', 'BadClass')
			->setManyToMany()
		;
	}

	public function testBadType()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'MetaData_Test_Entity::$id {m:m} Class \'Directory\' isn\'t instanceof Orm\ManyToMany');
		$this->m->addProperty('id', 'Directory')
			->setManyToMany()
		;
	}

	public function testTypeWithNamespaceSeparator1()
	{
		$p = $this->m->addProperty('id', '\Orm\ManyToMany')
			->setManyToMany('tests')
		;
		$this->t($p, 'Orm\ManyToMany', 'Orm\ManyToMany');
	}

	public function testTypeWithNamespaceSeparator2()
	{
		$p = $this->m->addProperty('id', '\ManyToMany_ManyToMany')
			->setManyToMany('tests')
		;
		$this->t($p, 'ManyToMany_ManyToMany', 'ManyToMany_ManyToMany');
	}

	public function testFunctionalWithoutClass()
	{
		$p = $this->m->addProperty('id', 'Orm\ManyToMany')
			->setManyToMany('MetaData_Test2')
		;
		$this->t($p, 'Orm\ManyToMany', 'MetaDataProperty_setManyToMany_ManyToMany'); // todo
	}

	public function testFunctionalWithoutClass2()
	{
		$p = $this->m->addProperty('id', '')
			->setManyToMany('MetaData_Test2')
		;
		$this->t($p, 'Orm\ManyToMany', 'MetaDataProperty_setManyToMany_ManyToMany'); // todo
	}

	public function testMapBoth()
	{
		MetaData::clean();
		MetaData_Test3_Entity::$mapped = true;
		MetaData_Test4_Entity::$mapped = true;
		$this->setExpectedException('Orm\RelationshipLoaderException', 'MetaData_Test4_Entity::$many a MetaData_Test3_Entity::$many {m:m} u ubou je nastaveno ze se na jeho strane ma mapovat, je potreba vybrat a mapovat jen podle jedne strany');
		MetaData::getEntityRules('MetaData_Test3_Entity', new RepositoryContainer);
	}

	public function testMapNone1()
	{
		MetaData::clean();
		MetaData_Test3_Entity::$mapped = false;
		MetaData_Test4_Entity::$mapped = false;
		$this->setExpectedException('Orm\RelationshipLoaderException', 'MetaData_Test4_Entity::$many a MetaData_Test3_Entity::$many {m:m} ani u jednoho neni nastaveno ze se podle neho ma mapovat. např: {m:m MetaData_Test3 many mapped}');
		MetaData::getEntityRules('MetaData_Test3_Entity', new RepositoryContainer);
	}

	public function testMapNone2()
	{
		MetaData::clean();
		MetaData_Test3_Entity::$mapped = NULL;
		MetaData_Test4_Entity::$mapped = NULL;
		$this->setExpectedException('Orm\RelationshipLoaderException', 'MetaData_Test4_Entity::$many a MetaData_Test3_Entity::$many {m:m} ani u jednoho neni nastaveno ze se podle neho ma mapovat. např: {m:m MetaData_Test3 many mapped}');
		MetaData::getEntityRules('MetaData_Test3_Entity', new RepositoryContainer);
	}

	public function testMapOk1()
	{
		MetaData::clean();
		MetaData_Test3_Entity::$mapped = true;
		MetaData_Test4_Entity::$mapped = false;
		MetaData::getEntityRules('MetaData_Test3_Entity', new RepositoryContainer);
		$this->assertTrue(true);
	}

	public function testMapOk2()
	{
		MetaData::clean();
		MetaData_Test3_Entity::$mapped = false;
		MetaData_Test4_Entity::$mapped = true;
		MetaData::getEntityRules('MetaData_Test3_Entity', new RepositoryContainer);
		$this->assertTrue(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setManyToMany');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

class MetaDataProperty_setManyToMany_ManyToMany extends OldManyToMany
{
}
