<?php

use Orm\EntityToArrayNoModeException;

/**
 * @covers Orm\EntityToArrayNoModeException
 */
class EntityToArrayNoModeException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityToArrayNoModeException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\EntityToArrayNoModeException', $e);
	}

	public function testMessage()
	{
		$e = new EntityToArrayNoModeException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new EntityToArrayNoModeException(array(new TestEntity, true, false));
		$this->assertSame('TestEntity::toArray() no mode for entity; use Orm\EntityToArray::ENTITY_AS_IS, ENTITY_AS_ID or ENTITY_AS_ARRAY.', $e->getMessage());
		$e = new EntityToArrayNoModeException(array(new TestEntity, false, true));
		$this->assertSame('TestEntity::toArray() no mode for entity; use Orm\EntityToArray::RELATIONSHIP_AS_IS, RELATIONSHIP_AS_ARRAY_OF_ID or RELATIONSHIP_AS_ARRAY_OF_ARRAY.', $e->getMessage());
	}
}
