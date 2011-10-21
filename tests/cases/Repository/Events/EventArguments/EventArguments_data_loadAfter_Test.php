<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_data_loadAfter_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testLoad()
	{
		$args = new EventArguments(Events::LOAD_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$this->assertSame(array('foo' => 'bar'), $args->data);
		$this->assertSame(false, isset($args->id));
	}

	public function testLoadWrite()
	{
		$args = new EventArguments(Events::LOAD_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$this->assertSame(array('foo' => 'bar'), $args->data);
		$args->data['foo'] = 598;
		$this->assertSame(array('foo' => 598), $args->data);
	}

	public function testLoadtNoData()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		new EventArguments(Events::LOAD_AFTER, $this->r, new TestEntity);
	}

	public function testLoadDataNotArray()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; '111' given.");
		new EventArguments(Events::LOAD_AFTER, $this->r, new TestEntity, array('data' => 111));
	}

	/**
	 * @dataProvider EventArguments_construct_Test::dataProviderTypes
	 */
	public function testReadData($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, array('id' => 123, 'data' => array('foo' => 'bar')));
		if ($type === Events::LOAD_AFTER OR $type === Events::LOAD_BEFORE)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$data.');
		}
		$args->data;
	}

	/**
	 * @dataProvider EventArguments_construct_Test::dataProviderTypes
	 */
	public function testWriteData($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, array('id' => 123, 'data' => array('foo' => 'bar')));
		if ($type === Events::LOAD_AFTER OR $type === Events::LOAD_BEFORE)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property Orm\EventArguments::$data.');
		}
		$args->data = 'xyz';
	}

}