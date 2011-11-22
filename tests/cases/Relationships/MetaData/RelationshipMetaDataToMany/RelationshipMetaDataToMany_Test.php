<?php

use Orm\RelationshipMetaDataToMany;

/**
 * @covers Orm\RelationshipMetaDataToMany
 */
class RelationshipMetaDataToMany_Test extends TestCase
{

	public function test()
	{
		$this->assertSame(true, RelationshipMetaDataToMany::MAPPED_HERE);
		$this->assertSame(false, RelationshipMetaDataToMany::MAPPED_THERE);
		$this->assertSame(2, RelationshipMetaDataToMany::MAPPED_BOTH);
	}

}
