<?php

require_once __DIR__ . '/../../../../boot.php';

class MetaDataProperty_setDefault_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$this->p = new MetaDataProperty($m, 'id', 'null');
	}

	private function setDefault()
	{
		$a = $this->p->toArray();
		return $a['default'];
	}

	public function test()
	{
		$this->p->setDefault(MetaData_Test_Entity::XXX);
		$this->assertEquals(MetaData_Test_Entity::XXX, $this->setDefault());
	}

}
