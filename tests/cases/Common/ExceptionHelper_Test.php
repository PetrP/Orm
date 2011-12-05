<?php

use Orm\ExceptionHelper;
use Orm\RepositoryContainer;

/**
 * @covers Orm\ExceptionHelper
 */
class ExceptionHelper_Test extends TestCase
{
	public function testNotArray()
	{
		$m = ExceptionHelper::format('ss', '');
		$this->assertSame('ss', $m);
	}

	public function testString()
	{
		$this->assertSame('ss', ExceptionHelper::format(array('ss'), '%s1'));
	}

	public function testClass()
	{
		$this->assertSame('ss', ExceptionHelper::format(array('ss'), '%c1'));
		$this->assertSame('ArrayObject', ExceptionHelper::format(array(new ArrayObject), '%c1'));
	}

	public function testType()
	{
		$this->assertSame('string', ExceptionHelper::format(array('ss'), '%t1'));
		$this->assertSame('ArrayObject', ExceptionHelper::format(array(new ArrayObject), '%t1'));
		$this->assertSame('integer', ExceptionHelper::format(array(123), '%t1'));
		$this->assertSame('NULL', ExceptionHelper::format(array(NULL), '%t1'));
	}

	public function testValue()
	{
		$this->assertSame('ss', ExceptionHelper::format(array('ss'), '%v1'));
		$this->assertSame('123', ExceptionHelper::format(array(123), '%v1'));
		$this->assertSame('123.58', ExceptionHelper::format(array(123.58), '%v1'));
		$this->assertSame('ArrayObject', ExceptionHelper::format(array(new ArrayObject), '%v1'));
		$this->assertSame('array', ExceptionHelper::format(array(array()), '%v1'));
		$this->assertSame('NULL', ExceptionHelper::format(array(NULL), '%v1'));
		$this->assertSame('boolean', ExceptionHelper::format(array(true), '%v1'));
		$this->assertSame('boolean', ExceptionHelper::format(array(false), '%v1'));
		$this->assertSame('', ExceptionHelper::format(array(''), '%v1'));
	}

	public function testEntity()
	{
		$e1 = new TestEntity;
		$m = new RepositoryContainer;
		$e2 = $m->tests->getById(1);
		$this->assertSame('TestEntity', ExceptionHelper::format(array($e1), '%e1'));
		$this->assertSame('TestEntity#1', ExceptionHelper::format(array($e2), '%e1'));
	}

	public function testMore()
	{
		$this->assertSame('a b c', ExceptionHelper::format(array('a', 'b', 'c'), '%s1 %s2 %s3'));
	}

	public function testIf()
	{
		$this->assertSame('abbbc', ExceptionHelper::format(array(true), 'a<%1%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false), 'a<%1%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(''), 'a<%1%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(NULL), 'a<%1%bbb>c'));
	}

	public function testIfOr()
	{
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, true), 'a<%1|2%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, false), 'a<%1|2%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(false, true), 'a<%1|2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, false), 'a<%1|2%bbb>c'));
	}

	public function testIfAnd()
	{
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, true), 'a<%1&2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(true, false), 'a<%1&2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, true), 'a<%1&2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, false), 'a<%1&2%bbb>c'));
	}

	public function testIfAndOr()
	{
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, true, true), 'a<%1&2|3%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, true, false), 'a<%1&2|3%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, false, true), 'a<%1&2|3%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(true, false, false), 'a<%1&2|3%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, true, true), 'a<%1&2|3%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, true, false), 'a<%1&2|3%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, false, true), 'a<%1&2|3%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, false, false), 'a<%1&2|3%bbb>c'));
	}

	public function testIfNot()
	{
		$this->assertSame('ac', ExceptionHelper::format(array(true), 'a<%!1%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(false), 'a<%!1%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(), 'a<%!1%bbb>c'));

		$this->assertSame('abbbc', ExceptionHelper::format(array(true, false), 'a<%1&!2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, false), 'a<%1&!2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, true), 'a<%1&!2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(true, true), 'a<%1&!2%bbb>c'));

		$this->assertSame('abbbc', ExceptionHelper::format(array(true, false), 'a<%1|!2%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(false, false), 'a<%1|!2%bbb>c'));
		$this->assertSame('ac', ExceptionHelper::format(array(false, true), 'a<%1|!2%bbb>c'));
		$this->assertSame('abbbc', ExceptionHelper::format(array(true, true), 'a<%1|!2%bbb>c'));
	}
}
