<?php

use Orm\MapperPersistenceException;

/**
 * @covers Orm\MapperPersistenceException
 */
class MapperPersistenceException_Test extends TestCase
{

	public function test()
	{
		$e = new MapperPersistenceException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MapperPersistenceException', $e);
	}

	public function testMessage()
	{
		$e = new MapperPersistenceException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new MapperPersistenceException(array($this, new TestEntity, 'abc', fopen(__FILE__, 'r')));
		$this->assertSame("MapperPersistenceException_Test: can't persist TestEntity::\$abc; it contains 'resource'.", $e->getMessage());
		$e = new MapperPersistenceException(array($this, new TestEntity, 'abc', new ArrayObject));
		$this->assertSame("MapperPersistenceException_Test: can't persist TestEntity::\$abc; it contains 'ArrayObject'.", $e->getMessage());
	}
}
