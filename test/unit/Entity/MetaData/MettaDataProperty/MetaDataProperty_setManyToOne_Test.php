<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::setManyToOne
 */
class MetaDataProperty_setManyToOne_Test extends TestCase
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

	public function testBase()
	{
		$p = $this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setManyToOne('MetaData_Test2')
		;

		$this->assertSame(MetaData::ManyToOne, $this->get($p));
		$this->assertSame('MetaData_Test2', $this->get($p, 'relationshipParam'));
	}

	public function testTwice()
	{
		$this->setExpectedException('InvalidStateException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setManyToOne('MetaData_Test2')
			->setManyToOne('MetaData_Test2')
		;
	}

	public function testNoRepo()
	{
		$this->setExpectedException('InvalidStateException', 'You must specify foreign repository in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setManyToOne('')
		;
	}

	public function testBadRepo()
	{
		$this->setExpectedException('InvalidStateException', 'BlaBlaBla isn\'t repository in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setManyToOne('BlaBlaBla')
		;
	}

}
