<?php

use Orm\IEntity;

/**
 * @covers Orm\ValueEntityFragment::getDefaultValueHelper
 */
class ValueEntityFragment_default_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_default_Entity;
	}

	public function testMeta()
	{
		$this->assertSame('meta', $this->e->meta);
	}

	public function testMethodProtected()
	{
		$this->assertSame('testMethod1', $this->e->testMethod1);
	}

	public function testMethodPublic()
	{
		$this->assertSame('testMethod2', $this->e->testMethod2);
	}

	public function testMethodPrivate()
	{
		$this->assertSame(NULL, $this->e->testMethod3);
	}

	public function testNoDefault()
	{
		$this->assertSame(NULL, $this->e->noDefault);
	}

	public function testMethodAndMeta()
	{
		$this->assertSame('meta', $this->e->testMethodAndMeta);
	}

	public function testCount()
	{
		$this->assertSame(0, $this->e->count);
		$this->e->testMethod1;
		$this->e->testMethod1;
		$this->e->testMethod1;
		$this->e->testMethod1;
		$this->assertSame(1, $this->e->count);
		$this->e->testMethod1 = 'blabla';
		$this->assertSame(1, $this->e->count);
	}

	public function testBackToDefault()
	{
		$this->assertSame('testMethod1', $this->e->testMethod1);
		$this->assertSame(1, $this->e->count);
		$this->e->testMethod1 = 'blabla';
		$this->assertSame('blabla', $this->e->testMethod1);
		$this->assertSame(1, $this->e->count);
		$this->e->testMethod1 = IEntity::DEFAULT_VALUE;
		$this->assertSame(2, $this->e->count);
		$this->assertSame('testMethod1', $this->e->testMethod1);
	}

	public function testSetterNoSet()
	{
		$this->assertSame(NULL, $this->e->setterNoSet);
		$this->assertSame(1, $this->e->countSetterNoSet);
		$this->assertSame(NULL, $this->e->setterNoSet);
		$this->assertSame(NULL, $this->e->setterNoSet);
		$this->assertSame(NULL, $this->e->setterNoSet);
		$this->assertSame(1, $this->e->countSetterNoSet);

		$this->e->setterNoSet = 'xxx';
		$this->assertSame(NULL, $this->e->setterNoSet);
		$this->assertSame(1, $this->e->countSetterNoSet);
	}
}
