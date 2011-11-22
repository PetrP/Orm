<?php

use Orm\RelationshipLoader;
use Orm\RelationshipMetaDataToMany;

/**
 * @covers Orm\RelationshipLoader
 */
class RelationshipLoader_Test extends TestCase
{
	public function test()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\RelationshipLoader is deprecated; use Orm\RelationshipMetaDataManyToMany or Orm\RelationshipMetaDataOneToMany instead.');
		new RelationshipLoader('', '', '', '', '', '');
	}

	public function testConst()
	{
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_HERE, RelationshipLoader::MAPPED_HERE);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_THERE, RelationshipLoader::MAPPED_THERE);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_BOTH, RelationshipLoader::MAPPED_BOTH);
	}
}
