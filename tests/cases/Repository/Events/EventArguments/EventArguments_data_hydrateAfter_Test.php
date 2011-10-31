<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_data_hydrateAfter_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function test()
	{
		$args = new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$this->assertSame(array('foo' => 'bar'), $args->data);
		$this->assertSame(false, isset($args->id));
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$this->assertSame(array('foo' => 'bar'), $args->data);
		$args->data['foo'] = 598;
		$this->assertSame(array('foo' => 598), $args->data);
	}

	public function testNoData()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity);
	}

	public function testDataNotArray()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; '111' given.");
		new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => 111));
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testReadData($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::HYDRATE_AFTER OR $type === Events::HYDRATE_BEFORE)
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
	 * @dataProvider dataProviderAll
	 */
	public function testWriteData($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::HYDRATE_AFTER OR $type === Events::HYDRATE_BEFORE)
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
