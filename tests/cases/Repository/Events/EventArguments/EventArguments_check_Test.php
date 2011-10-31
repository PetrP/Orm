<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::check
 */
class EventArguments_check_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EventArguments', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testId()
	{
		$args = new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => 123));
		$args->id = 598;
		$args->check();
		$this->assertTrue(true);
	}

	public function testNoId()
	{
		$args = new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => 123));
		$args->id = NULL;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$id must be scalar; 'NULL' given.");
		$args->check();
	}

	public function testIdNotScalar()
	{
		$args = new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => 123));
		$args->id = array();
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$id must be scalar; 'array' given.");
		$args->check();
	}

	public function testData()
	{
		$args = new EventArguments(Events::HYDRATE_BEFORE, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data['foo'] = 598;
		$args->check();
		$this->assertTrue(true);
	}

	public function testNoData()
	{
		$args = new EventArguments(Events::HYDRATE_BEFORE, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data = NULL;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		$args->check();
	}

	public function testDataNotArray()
	{
		$args = new EventArguments(Events::HYDRATE_BEFORE, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data = 111;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; '111' given.");
		$args->check();
	}

	public function testDataAfter()
	{
		$args = new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data['foo'] = 598;
		$args->check();
		$this->assertTrue(true);
	}

	public function testNoDataAfter()
	{
		$args = new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data = NULL;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; 'NULL' given.");
		$args->check();
	}

	public function testDataNotArrayAfter()
	{
		$args = new EventArguments(Events::HYDRATE_AFTER, $this->r, new TestEntity, array('data' => array('foo' => 'bar')));
		$args->data = 111;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$data must be array; '111' given.");
		$args->check();
	}

	public function testParams()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$args->params['foo'] = 598;
		$args->check();
		$this->assertTrue(true);
	}

	public function testNoParams()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$args->params = NULL;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$params must be array; 'NULL' given.");
		$args->check();
	}

	public function testParamsNotArray()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$args->params = 111;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$params must be array; '111' given.");
		$args->check();
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testValues($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$args->values['foo'] = 598;
		$args->check();
		$this->assertTrue(true);
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testNoValues($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$args->values = NULL;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$values must be array; 'NULL' given.");
		$args->check();
	}

	/**
	 * @dataProvider dataProviderValuesTypes
	 */
	public function testValuesNotArray($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$args->values = 111;
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$values must be array; '111' given.");
		$args->check();
	}
}
