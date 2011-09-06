<?php

use Orm\RelationshipLoader;

/**
 * @covers Orm\RelationshipLoader
 */
class RelationshipLoader_Test extends TestCase
{

	public function test()
	{
		$this->assertSame(true, RelationshipLoader::MAPPED_HERE);
		$this->assertSame(false, RelationshipLoader::MAPPED_THERE);
		$this->assertSame(2, RelationshipLoader::MAPPED_BOTH);
	}

}
