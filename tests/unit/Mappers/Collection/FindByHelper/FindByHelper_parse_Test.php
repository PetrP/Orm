<?php

use Orm\FindByHelper;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\FindByHelper::parse
 */
class FindByHelper_parse_Test extends TestCase
{
	private function t($method, array $args)
	{
		$r = FindByHelper::parse($method, $args);
		return array($r, $method, $args);
	}

	public function testFindBy()
	{
		$this->assertSame(array(true, 'findBy', array('foo' => 'bar')), $this->t('findByFoo', array('bar')));
	}

	public function testGetBy()
	{
		$this->assertSame(array(true, 'getBy', array('foo' => 'bar')), $this->t('getByFoo', array('bar')));
	}

	public function testFindByIgnoreCase()
	{
		$this->assertSame(array(true, 'findBy', array('foo' => 'bar')), $this->t('FINDbYFoo', array('bar')));
	}

	public function testGetByIgnoreCase()
	{
		$this->assertSame(array(true, 'getBy', array('foo' => 'bar')), $this->t('GETbYFoo', array('bar')));
	}

	public function testNoMethod()
	{
		$this->assertSame(array(false, 'getFooBar', array('bar')), $this->t('getFooBar', array('bar')));
	}

	public function testExtraParam()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "There is extra value in 'findByFoo'.");
		$this->t('findByFoo', array('bar', 'foo'));
	}

	public function testMissingParam()
	{
		$this->setExpectedException('Nette\InvalidArgumentException', "There is no value for 'bar' in 'findByFooAndBar'.");
		$this->t('findByFooAndBar', array('bar'));
	}

}