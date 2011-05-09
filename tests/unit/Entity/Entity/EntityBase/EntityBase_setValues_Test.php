<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityBase::setValues
 */
class EntityBase_setValues_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$this->e = new EntityBase_setValues_Entity;
	}

	public function testMeta()
	{
		$this->e->setValues(array('meta' => 123));
		$this->assertSame(123, $this->e->meta);
	}

	public function testMetaPrivate()
	{
		$this->e->setValues(array('metaPrivate' => 123));
		$this->assertSame(NULL, $this->e->metaPrivate);
	}

	public function testProperty()
	{
		$this->e->setValues(array('property' => 123));
		$this->assertSame(123, $this->e->property);
	}

	public function testPropertyPrivate()
	{
		$this->e->setValues(array('propertyPrivate' => 123));
		$this->assertAttributeSame(NULL, 'propertyPrivate', $this->e);
	}

	public function testPropertyPrivate2()
	{
		$this->e->setValues(array('propertyPrivate2' => 123));
		$this->assertAttributeSame(NULL, 'propertyPrivate2', $this->e);
	}

	public function testMethod()
	{
		$this->e->setValues(array('method' => 123));
		$this->assertSame(123, $this->e->_method);
	}

	public function testMethodPrivate()
	{
		$this->e->setValues(array('methodPrivate' => 123));
		$this->assertTrue(true);
	}

	public function testMethodPrivate2()
	{
		$this->e->setValues(array('methodPrivate2' => 123));
		$this->assertTrue(true);
	}
}
