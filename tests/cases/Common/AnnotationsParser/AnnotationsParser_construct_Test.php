<?php

use Orm\AnnotationsParser;

/**
 * @covers Orm\AnnotationsParser::__construct
 */
class AnnotationsParser_construct_Test extends TestCase
{

	public function testCustomImplementation()
	{
		$f = function () { return 123; };
		$p = new AnnotationsParser($f);
		$this->assertAttributeSame($f, 'callback', $p);
		$this->assertSame(123, $p->getByReflection(new ReflectionClass($this)));
	}

	public function testAutoDetectNette()
	{
		$p = new AnnotationsParser;
		$this->assertAttributeSame(array('Nette\Reflection\AnnotationsParser', 'getAll'), 'callback', $p);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationsParser', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
