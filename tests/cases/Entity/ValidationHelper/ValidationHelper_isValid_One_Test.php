<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::isValid
 */
class ValidationHelper_isValid_One_Test extends ValidationHelper_isValid_Base
{
	public function testNull()
	{
		$this->type = 'null';
		$this->t(NULL, true);
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
	}

	public function testBool()
	{
		$this->type = 'bool';
		$this->t(NULL, false);
		$this->t(false, true);
		$this->t(true, true);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', true, false);
		$this->t('1', true, true);
		$this->t(1, true, true);
		$this->t(0, true, false);
		$this->t(1.0, true, true);
		$this->t(0.0, true, false);
		$this->t(2, false);
		$this->t(5.69, false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
		$this->t(fopen(__FILE__, 'r'), false);
	}

	public function testInt()
	{
		$this->type = 'int';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', true, 0);
		$this->t('1', true, 1);
		$this->t(1, true);
		$this->t(0, true);
		$this->t(5.69, true, 5);
		$this->t('5.69', true, 5);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testFloat()
	{
		$this->type = 'float';
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);
		$this->t(' ', false);
		$this->t('xx', false);
		$this->t("\0", false);
		$this->t('0', true, 0.0);
		$this->t('1', true, 1.0);
		$this->t(1, true, 1.0);
		$this->t(0, true, 0.0);
		$this->t(5.69, true, 5.69);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);
	}

	public function testString()
	{
		$this->type = 'string';
		$this->t(NULL, false);
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
		$this->t((object) array(), true, array()); // wtf?
		$this->t(new ArrayObject, true, array());

		$this->t((object) array('xx' => 'aa'), true, array('xx' => 'aa')); // wtf?
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
		$this->t(array(), true, (object) array(), false);
		$this->t(array('xx' => 'aa'), true, (object) array('xx' => 'aa'), false);
		$this->t((object) array(), true);
		$this->t(new ArrayObject, true);

		$this->t((object) array('xx' => 'aa'), true);
		$this->t(new ArrayObject(array('xx' => 'aa')), true);
		$this->t(serialize((object) array('xx' => 'aa')), false); // todo

		$this->t(new Directory, true);
	}

	public function testDateTime()
	{
		$this->type = 'datetime';
		$this->t('2010-11-11', true, ValidationHelper::createDateTime('2010-11-11'), false);
		$this->t(NULL, false);
		$this->t(false, false);
		$this->t(true, false);
		$this->t('', false);

		try {
			$this->t('xx', false);
			$this->fail();
		} catch (Exception $e) {
			$this->assertSame($e->getMessage(), 'DateTime::__construct(): Failed to parse time string (xx) at position 0 (x): The timezone could not be found in the database');
		}

		$this->t('0', true, ValidationHelper::createDateTime('now'), false);
		$this->t('1', true, ValidationHelper::createDateTime('+1 second'), false);
		$this->t(1, true, ValidationHelper::createDateTime('+1 second'), false);
		$this->t(0, true, ValidationHelper::createDateTime('now'), false);
		$this->t(5.69, true, ValidationHelper::createDateTime('+5 second'), false);
		$this->t(array(), false);
		$this->t(array('xx' => 'aa'), false);
		$this->t((object) array(), false);
		$this->t(new ArrayObject, false);

		$this->t(new DateTime('2011-11-11'), true);
		$this->t('-1 month', true, ValidationHelper::createDateTime('-1 month'), false);

		if (PHP_VERSION_ID <= 50203)
		{
			// pravdepodobne "opraveno" s bugem #41964
			try {
				$this->t(' ', true, ValidationHelper::createDateTime('now'), false);
				$this->fail();
			} catch (Exception $e) {
				$this->assertSame($e->getMessage(), 'DateTime::__construct(): Failed to parse time string ( ) at position 0 (');
			}
			try {
				$this->t("\0", true, ValidationHelper::createDateTime('now'), false);
				$this->fail();
			} catch (Exception $e) {
				$this->assertSame($e->getMessage(), 'DateTime::__construct(): Failed to parse time string () at position 0 (');
			}
		}
		else
		{
			$this->t(' ', true, ValidationHelper::createDateTime('now'), false);
			$this->t("\0", true, ValidationHelper::createDateTime('now'), false);
		}
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
		$this->t(serialize(new ArrayObject(array('xx' => 'aa'))), true, new ArrayObject(array('xx' => 'aa')), false);
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
		$this->type = 'directory';
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

		$this->t(new Directory, true);
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValidationHelper', 'isValid');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
