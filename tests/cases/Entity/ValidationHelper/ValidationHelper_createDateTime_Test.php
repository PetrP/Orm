<?php

use Orm\ValidationHelper;

/**
 * @covers Orm\ValidationHelper::createDateTime
 */
class ValidationHelper_createDateTime_Test extends TestCase
{

	public function testReturn()
	{
		$this->assertInstanceOf('DateTime', ValidationHelper::createDateTime('now'));
		$this->assertInstanceOf('DateTime', ValidationHelper::createDateTime(300));
		$this->assertInstanceOf('DateTime', ValidationHelper::createDateTime(time()));
		$this->assertInstanceOf('DateTime', ValidationHelper::createDateTime(new DateTime));
	}

	public function testString()
	{
		$this->assertSame('2011-11-11T00:00:00+01:00', ValidationHelper::createDateTime('2011-11-11')->format('c'));
	}

	public function testInt()
	{
		$this->assertSame('2011-07-22T16:59:50+02:00', ValidationHelper::createDateTime(1311346790)->format('c'));
		$this->assertSame('2011-07-22T16:59:50+02:00', ValidationHelper::createDateTime('1311346790')->format('c'));
	}

	public function testIntYear()
	{
		$this->assertSame((string) (time() + 31557600), ValidationHelper::createDateTime(31557600)->format('U'));
		$this->assertSame((string) (31557600+1), ValidationHelper::createDateTime(31557600+1)->format('U'));
		$this->assertSame((string) (time() + 31557600-1), ValidationHelper::createDateTime(31557600-1)->format('U'));
		$this->assertSame((string) (time() + 5), ValidationHelper::createDateTime('5')->format('U'));
	}

	public function testClone()
	{
		$d1 = new DateTime('2011-11-11');
		$d2 = ValidationHelper::createDateTime($d1);
		$this->assertSame('2011-11-11T00:00:00+01:00', $d2->format('c'));
		$this->assertEquals($d1, $d2);
		$this->assertNotSame($d1, $d2);
	}

	public function testEmpty()
	{
		$this->assertSame((string) time(), ValidationHelper::createDateTime(NULL)->format('U'));
		$this->assertSame((string) time(), ValidationHelper::createDateTime('')->format('U'));
		$this->assertSame((string) time(), ValidationHelper::createDateTime('0')->format('U'));
		$this->assertSame((string) time(), ValidationHelper::createDateTime(0)->format('U'));
		$this->assertSame((string) time(), ValidationHelper::createDateTime(false)->format('U'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValidationHelper', 'createDateTime');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
