<?php

use Orm\MetaData;
use Orm\OldOneToMany;
use Orm\MetaDataProperty;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::setOneToMany
 * @covers MetaDataProperty::setToMany
 * @covers RelationshipLoader::__construct
 */
class MetaDataProperty_setOneToMany_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		new RepositoryContainer;
		$this->m = new MetaData('MetaData_Test_Entity');
	}

	private function get(MetaDataProperty $p, $key = 'relationship')
	{
		$a = $p->toArray();
		return $a[$key];
	}

	private function t(MetaDataProperty $p, $class, $name)
	{
		$this->assertSame(MetaData::OneToMany, $this->get($p));

		$i = $this->get($p, 'injection');
		$this->assertInstanceOf('Nette\Callback', $i);

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
		$p = $this->m->addProperty('id', 'MetaDataProperty_setOneToMany_OneToMany')
			->setOneToMany()
		;
		$this->t($p, 'MetaDataProperty_setOneToMany_OneToMany', 'MetaDataProperty_setOneToMany_OneToMany');
	}

	public function testTwice()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaDataProperty_setOneToMany_OneToMany')
			->setOneToMany()
			->setOneToMany()
		;
	}

	public function testMultipleType()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'MetaData_Test_Entity::$id {1:m} excepts Orm\OneToMany class as type, \'string|int\' given');
		$this->m->addProperty('id', 'string|int')
			->setOneToMany()
		;
	}

	public function testBadType_unexist()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'MetaData_Test_Entity::$id {1:m} excepts Orm\OneToMany class as type, class \'BadClass\' doesn\'t exists');
		$this->m->addProperty('id', 'BadClass')
			->setOneToMany()
		;
	}

	public function testBadType()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'MetaData_Test_Entity::$id {1:m} Class \'Nette\Utils\Html\' isn\'t instanceof Orm\OneToMany');
		$this->m->addProperty('id', 'Nette\Utils\Html')
			->setOneToMany()
		;
	}

	public function testFunctionalWithoutClass()
	{
		$p = $this->m->addProperty('id', 'Orm\OneToMany')
			->setOneToMany('MetaData_Test2', 'param')
		;
		$this->t($p, 'Orm\OneToMany', 'MetaDataProperty_setOneToMany_OneToMany'); // todo
	}

	public function testFunctionalWithoutClass2()
	{
		$p = $this->m->addProperty('id', '')
			->setOneToMany('MetaData_Test2', 'param')
		;
		$this->t($p, 'Orm\OneToMany', 'MetaDataProperty_setOneToMany_OneToMany'); // todo
	}
}

class MetaDataProperty_setOneToMany_OneToMany extends OldOneToMany
{
}
