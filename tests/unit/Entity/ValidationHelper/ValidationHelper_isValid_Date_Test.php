<?php

/**
 * @covers Orm\ValidationHelper::isValid
 */
class ValidationHelper_isValid_Date_Test extends ValidationHelper_isValid_Base
{
	public function test1()
	{
		$this->type = 'datetime|string';
		$this->t('xxx', true);
	}

	public function test2()
	{
		$this->type = 'datetime';
		$this->setExpectedException('Exception', 'DateTime::__construct(): Failed to parse time string (xxx) at position 0 (x): The timezone could not be found in the database');
		$this->t('xxx', false);
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
