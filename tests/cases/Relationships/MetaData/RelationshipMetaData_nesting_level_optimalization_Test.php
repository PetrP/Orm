<?php

use Orm\RepositoryContainer;
use Orm\MetaData;

/**
 * @covers Orm\MetaData::getEntityRules
 */
class RelationshipMetaData_nesting_level_optimalization_Test extends TestCase
{

	protected function setUp()
	{
		parent::setUp();
		class_exists('RelationshipMetaData_nesting_level_optimalization_ARepository');
	}

	public function test1()
	{
		$orm = new RepositoryContainer;
		$this->assertInternalType('array', MetaData::getEntityRules('RelationshipMetaData_nesting_level_optimalization_X_1', $orm));
		$this->assertInternalType('array', MetaData::getEntityRules('RelationshipMetaData_nesting_level_optimalization_X_100', $orm));
	}

}
