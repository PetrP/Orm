<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers ValidationHelper::isValid
 */
class ValidationHelper_isValid_One_Test extends ValidationHelper_isValid_Base
{
	public function testNull()
	{
		$this->type = 'null';
		$this->t(NULL, true);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false); // todo nemelo by fungovat?
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', false);
		$this->t('1', false);
		$this->t(1, false);
		$this->t(0, false);
		$this->t(5.69, false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testBool()
	{
		$this->type = 'bool';
		$this->t(NULL, true, false);
		$this->t(false, true);
		$this->t(true, true);
		$this->t('', true, false);
		$this->t(' ', true, true); // wtf
		$this->t('xx', true, true); // wtf
		$this->t("\0", true, true); // wtf
		$this->t('0', true, false);
		$this->t('1', true, true);
		$this->t(1, true, true);
		$this->t(0, true, false);
		$this->t(5.69, true, true); // wtf
		$this->t(array(), true, false);
		$this->t(array('xx' => 'aa'), true, true); // wtf
		$this->t((object) array(), true, true); // wtf
		$this->t(new ArrayObject, true, true); // wtf
		$this->t(fopen(__FILE__, 'r'), true, true); // wtf
	}

	public function testInt()
	{
		$this->type = 'int';
		$this->t(NULL, true, 0);
		$this->t(false, true, 0);
		$this->t(true, false); // wtf
		$this->t('', true, 0);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', true, 0);
		$this->t('1', true, 1);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true, 5);
		$this->t('5.69', true, 5);
		$this->t(array(), true, 0); // wtf
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testFloat()
	{
		$this->type = 'float';
		$this->t(NULL, true, 0.0);
		$this->t(false, true, 0.0);
		$this->t(true, false); // wtf
		$this->t('', true, 0.0);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', true, 0.0);
		$this->t('1', true, 1.0);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true, 5.69);
		$this->t(array(), true, 0.0); // wtf
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testString()
	{
		$this->type = 'string';
		$this->t(NULL, true, '');
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', true);
		$this->t(' ', true);
		$this->t('xx', true);
		$this->t("\0", true);
		$this->t('0', true);
		$this->t('1', true);
		$this->t(1, true, '1');
		$this->t(0, true, '0');
		$this->t(5.69, true, '5.69');
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testArray()
	{
		$this->type = 'array';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', false);
		$this->t('1', false);
		$this->t(1, false);
		$this->t(0, false);
		$this->t(5.69, false);
		$this->t(array(), true);
		$this->t(array('xx' => 'aa'), true);
		$this->t((object) array(), true, array());
		$this->t(new ArrayObject, true, array());

		$this->t((object) array('xx' => 'aa'), true, array('xx' => 'aa'));
		$this->t(new ArrayObject(array('xx' => 'aa')), true, array('xx' => 'aa'));
		$this->t(serialize(array('xx' => 'aa')), true, array('xx' => 'aa'));
		$this->t(serialize((object) array('xx' => 'aa')), false);
		$this->t(serialize(new ArrayObject(array('xx' => 'aa'))), false);
	}

	public function testObject()
	{
		$this->type = 'object';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', false);
		$this->t('1', false);
		$this->t(1, false);
		$this->t(0, false);
		$this->t(5.69, false);
		$this->t(array(), true, (object) array());
		$this->t(array('xx' => 'aa'), true, (object) array('xx' => 'aa'));
		$this->t((object) array(), true);
		$this->t(new ArrayObject, true);

		$this->t((object) array('xx' => 'aa'), true);
		$this->t(new ArrayObject(array('xx' => 'aa')), true);
		$this->t(serialize((object) array('xx' => 'aa')), false); // todo

		$this->t(Html::el(), true); // todo
	}

	public function testDateTime()
	{
		$this->type = 'datetime';
		$this->t('2010-11-11', true, Tools::createDateTime('2010-11-11'));
		$this->t(NULL, true, Tools::createDateTime('now'));
		$this->t(false, true, Tools::createDateTime('now')); // wtf
		try {
			$this->t(true, false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct(): Failed to parse time string (1) at position 0 (1): Unexpected character');
		}
		$this->t('', true, Tools::createDateTime('now'));
		$this->t(' ', true, Tools::createDateTime('now')); // wtf
		try {
			$this->t('xx', false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct(): Failed to parse time string (xx) at position 0 (x): The timezone could not be found in the database');
		}
		$this->t("\0", true, Tools::createDateTime('now'));
		$this->t('0', true, Tools::createDateTime('now'));
		$this->t('1', true, Tools::createDateTime('+1 second'));
		$this->t(1, true, Tools::createDateTime('+1 second'));
		$this->t(0, true, Tools::createDateTime('now'));
		$this->t(5.69, true, Tools::createDateTime('+5 second'));
		try {
			$this->t(array(), false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct() expects parameter 1 to be string, array given');
		}
		try {
			$this->t(array('xx' => 'aa'), false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct() expects parameter 1 to be string, array given');
		}
		try {
			$this->t((object) array(), false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct() expects parameter 1 to be string, object given');
		}
		try {
			$this->t(new ArrayObject, false);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'DateTime::__construct() expects parameter 1 to be string, object given');
		}

		$this->t(new DateTime('2011-11-11'), true);
		$this->t('-1 month', true, Tools::createDateTime('-1 month'));
	}

	public function testArrayObject()
	{
		$this->type = 'arrayobject';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', false);
		$this->t('1', false);
		$this->t(1, false);
		$this->t(0, false);
		$this->t(5.69, false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, true);

		$this->t((object) array('xx' => 'aa'), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t(new ArrayObject(array('xx' => 'aa')), true);
		$this->t(serialize(array('xx' => 'aa')), false);
		$this->t(serialize((object) array('xx' => 'aa')), false);
		$this->t(serialize(new ArrayObject(array('xx' => 'aa'))), true, new ArrayObject(array('xx' => 'aa')));
	}

	public function testMixed()
	{
		$this->type = array();
		$this->t(NULL, true);
		$this->t(false, true);
		$this->t(true, true);
		$this->t('', true);
		$this->t(' ', true);
		$this->t('xx', true);
		$this->t("\0", true);
		$this->t('0', true);
		$this->t('1', true);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true);
		$this->t(array(), true);
		$this->t(array('xx' => 'aa'), true);
		$this->t((object) array(), true);
		$this->t(new ArrayObject, true);

		$this->type = 'mixed';
		$this->t(NULL, true);
		$this->t(false, true);
		$this->t(true, true);
		$this->t('', true);
		$this->t(' ', true);
		$this->t('xx', true);
		$this->t("\0", true);
		$this->t('0', true);
		$this->t('1', true);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true);
		$this->t(array(), true);
		$this->t(array('xx' => 'aa'), true);
		$this->t((object) array(), true);
		$this->t(new ArrayObject, true);
	}

	public function testInstanceof()
	{
		$this->type = 'html';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', false);
		$this->t('1', false);
		$this->t(1, false);
		$this->t(0, false);
		$this->t(5.69, false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);

		$this->t(new Html, true);
	}

	public function testScalar()
	{
		$this->type = 'scalar';
		$this->t(NULL, false);
		$this->t(false, true);
		$this->t(true, true);
		$this->t('', true);
		$this->t(' ', true);
		$this->t('xx', true);
		$this->t("\0", true);
		$this->t('0', true);
		$this->t('1', true);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testId()
	{
		$this->type = 'id';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t(5.69, false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);

		$this->t(0, false);
		$this->t('0', false);
		$this->t(0.0, false);

		$this->t(-1, false);
		$this->t('-1', false);
		$this->t(-1.0, false);

		$this->t(1, true);
		$this->t('1', true);
		$this->t(1.0, true);
		$this->t(PHP_INT_MAX, true);
		$this->t(str_repeat('123', 100000), true);
		$this->t((float) 100000000000000-1, true);
		$this->t((float) 100000000000000, false); // 1.0E+14
	}

}
