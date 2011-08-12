<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::clean
 */
class MetaData_clean_Test extends TestCase
{

	public function test()
	{
		MetaData::getEntityRules('MetaData_Test_Entity');
		$this->assertAttributeNotEmpty('cache', 'Orm\MetaData');
		MetaData::clean();
		$this->assertAttributeEmpty('cache', 'Orm\MetaData');
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
	}

}
