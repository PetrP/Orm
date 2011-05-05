<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::setManyToMany
 * @covers MetaDataProperty::setToMany
 * @covers RelationshipLoader::__construct
 */
class MetaDataProperty_setManyToMany_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		new Model;
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
		$this->assertInstanceOf('Callback', $i);

		$ii = $i->getNative();
		$this->assertInstanceOf('InjectionFactory', $ii[0]);
		$this->assertAttributeSame($class, 'className', $ii[0]);

		$ii = $this->readAttribute($ii[0], 'callback');
		$this->assertInstanceOf('RelationshipLoader', $ii[0]);
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
		$this->setExpectedException('InvalidStateException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaDataProperty_setManyToMany_ManyToMany')
			->setManyToMany()
			->setManyToMany()
		;
	}

	public function testMultipleType()
	{
		$this->setExpectedException('InvalidStateException', 'MetaData_Test_Entity::$id {m:m} excepts ManyToMany class as type, \'string|int\' given');
		$this->m->addProperty('id', 'string|int')
			->setManyToMany()
		;
	}

	public function testBadType_unexist()
	{
		$this->setExpectedException('InvalidStateException', 'MetaData_Test_Entity::$id {m:m} excepts ManyToMany class as type, class \'BadClass\' doesn\'t exists');
		$this->m->addProperty('id', 'BadClass')
			->setManyToMany()
		;
	}

	public function testBadType()
	{
		$this->setExpectedException('InvalidStateException', 'MetaData_Test_Entity::$id {m:m} Class \'Html\' isn\'t instanceof ManyToMany');
		$this->m->addProperty('id', 'Html')
			->setManyToMany()
		;
	}

	public function testFunctionalWithoutClass()
	{
		$p = $this->m->addProperty('id', 'ManyToMany')
			->setManyToMany('MetaData_Test2')
		;
		$this->t($p, 'ManyToMany', 'MetaDataProperty_setManyToMany_ManyToMany'); // todo
	}

	public function testFunctionalWithoutClass2()
	{
		$p = $this->m->addProperty('id', '')
			->setManyToMany('MetaData_Test2')
		;
		$this->t($p, 'ManyToMany', 'MetaDataProperty_setManyToMany_ManyToMany'); // todo
	}
}

class MetaDataProperty_setManyToMany_ManyToMany extends OldManyToMany
{
}
