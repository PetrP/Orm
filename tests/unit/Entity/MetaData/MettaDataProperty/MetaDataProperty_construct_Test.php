<?php

use Orm\MetaData;
use Orm\MetaDataProperty;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::__construct
 * @covers MetaDataProperty::getSince
 */
class MetaDataProperty_construct_Test extends TestCase
{
	private function t($name)
	{
		$m = new MetaData('MetaData_Test_Entity');
		return new MetaDataProperty($m, $name, 'types', MetaData::READWRITE, 'since');
	}

	public function test()
	{
		$property = $this->t('id');
		$this->assertEquals(
			array(
				'types' => array('types' => 'types'),
				'get' => array('method' => 'getId'),
				'set' => array('method' => NULL),
				'since' => 'since',
				'relationship' => NULL,
				'relationshipParam' => NULL,
				'default' => NULL,
				'enum' => NULL,
				'injection' => NULL,
			)
			, $a = $property->toArray()
		);
		$this->assertEquals('since', $property->getSince());
		$this->assertAttributeEquals('id', 'name', $property);
		$this->assertAttributeEquals('MetaData_Test_Entity', 'class', $property);
	}

	public function testAscii()
	{
		$this->t('abc');
		$this->t('AbC');
		$this->t('1abc');
		$this->t('54BcA');
		$this->t('s_54BcA');
		$this->t('0123456789qwertyuiopasdfghjklzxcvbnm_');
		$this->assertTrue(true);
	}

	public function testNonAsciiName1()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, 'ábč' given");
		$this->t('ábč');
	}

	public function testNonAsciiNameEmpty()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, '' given");
		$this->t('');
	}

	public function testNonAsciiName2()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, 'ab-c' given");
		$this->t('ab-c');
	}

	public function testNonAsciiName3()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, '");
		$this->t("\0");
	}

	public function testNonAsciiName4()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, 'abc\tasd' given");
		$this->t("abc\tasd");
	}

	public function testNonAsciiName5()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, 'abc asd' given");
		$this->t("abc asd");
	}

	public function testNonAsciiName6()
	{
		$this->setExpectedException('InvalidArgumentException', "MetaData_Test_Entity property name must be non-empty alphanumeric string, '&' given");
		$this->t("&");
	}

}
