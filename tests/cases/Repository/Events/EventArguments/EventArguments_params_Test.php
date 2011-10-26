<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_params_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function test()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$this->assertSame(array('foo' => true), $args->params);
		$this->assertSame(false, isset($args->id));
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$this->assertSame(array('foo' => true), $args->params);
		$args->params['foo'] = 598;
		$this->assertSame(array('foo' => 598), $args->params);
	}

	public function testNoParams()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$params must be array; 'NULL' given.");
		$args = $this->args;
		unset($args['params']);
		new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $args);
	}

	public function testParamsNotArray()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$params must be array; '111' given.");
		$args = $this->args;
		$args['params'] = 111;
		new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $args);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testReadParams($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::SERIALIZE_BEFORE)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$params.');
		}
		$args->params;
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testWriteParams($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::SERIALIZE_BEFORE)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property Orm\EventArguments::$params.');
		}
		$args->params = 'xyz';
	}

}
