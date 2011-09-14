<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::isValid
 */
class ValidationHelper_isValid_Multiple_Test extends ValidationHelper_isValid_Base
{
	public function testNull()
	{
		$this->type = 'bool|null';
		$this->t(NULL, true);
		$this->t(false, true);
		$this->t('', false);

		$this->type = 'int|null';
		$this->t(NULL, true);
		$this->t(1, true);
		$this->t(5.98, true, 5);
		$this->t('xxx', false);

		$this->type = 'float|null';
		$this->t(NULL, true);
		$this->t(1, true, 1.0);
		$this->t(5.98, true, 5.98);
		$this->t('xxx', false);

		$this->type = 'string|null';
		$this->t(NULL, true);
		$this->t('xxx', true);
		$this->t(1, true, '1');
		$this->t(array(), false);

		$this->type = 'array|null';
		$this->t(NULL, true);
		$this->t(array(), true);
		$this->t((object) array('xx' => 'aa'), true, array('xx' => 'aa'));
		$this->t('', false);

		$this->type = 'object|null';
		$this->t(NULL, true);
		$this->t(new Directory, true);
		$this->t(array(), true, (object) array(), false);
		$this->t('', false);

		$this->type = 'datetime|null';
		$this->t(NULL, true);
		$this->t(new DateTime, true);
		$this->t('now', true, ValidationHelper::createDateTime('now'), false);

		$this->type = 'arrayobject|null';
		$this->t(NULL, true);
		$this->t(new ArrayObject, true);
		$this->t(serialize(new ArrayObject(array('xx' => 'aa'))), true, new ArrayObject(array('xx' => 'aa')), false);
		$this->t('', false);

		$this->type = 'mixed|null';
		$this->t(NULL, true);
		$this->t('xxx', true);

		$this->type = 'directory|null';
		$this->t(NULL, true);
		$this->t(new Directory, true);
		$this->t('', false);
		$this->t(new DateTime, false);
	}

	public function testStringInt()
	{
		$this->type = 'string|int';
		$this->t(NULL, false);
		$this->t(0, true);
		$this->t('0', true);
		$this->t('xxx', true);
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
