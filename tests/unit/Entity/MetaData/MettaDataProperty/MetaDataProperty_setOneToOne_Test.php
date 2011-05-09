<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::setOneToOne
 */
class MetaDataProperty_setOneToOne_Test extends TestCase
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
			->setOneToOne('MetaData_Test2')
		;

		$this->assertSame(MetaData::OneToOne, $this->get($p));
		$this->assertSame('MetaData_Test2', $this->get($p, 'relationshipParam'));
	}

	public function testTwice()
	{
		$this->setExpectedException('InvalidStateException', 'Already has relationship in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('MetaData_Test2')
			->setOneToOne('MetaData_Test2')
		;
	}

	public function testNoRepo()
	{
		$this->setExpectedException('InvalidStateException', 'You must specify foreign repository in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('')
		;
	}

	public function testBadRepo()
	{
		$this->setExpectedException('InvalidStateException', 'BlaBlaBla isn\'t repository in MetaData_Test_Entity::$id');
		$this->m->addProperty('id', 'MetaData_Test2_Entity')
			->setOneToOne('BlaBlaBla')
		;
	}

}
