<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_values_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function test($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$this->assertSame(array('foo' => 'bar'), $args->values);
		$this->assertSame(false, isset($args->id));
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testWrite($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$this->assertSame(array('foo' => 'bar'), $args->values);
		$args->values['foo'] = 598;
		$this->assertSame(array('foo' => 598), $args->values);
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testNoValues($type)
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$values must be array; 'NULL' given.");
		$args = $this->args;
		unset($args['values']);
		new EventArguments($type, $this->r, new TestEntity, $args);
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testValuesNotArray($type)
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$values must be array; '111' given.");
		$args = $this->args;
		$args['values'] = 111;
		new EventArguments($type, $this->r, new TestEntity, $args);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testReadValues($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type & (Events::SERIALIZE_BEFORE | Events::SERIALIZE_AFTER | Events::SERIALIZE_CONVENTIONAL))
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$values.');
		}
		$args->values;
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testWriteValues($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type & (Events::SERIALIZE_BEFORE | Events::SERIALIZE_AFTER | Events::SERIALIZE_CONVENTIONAL))
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property Orm\EventArguments::$values.');
		}
		$args->values = 'xyz';
	}

}
