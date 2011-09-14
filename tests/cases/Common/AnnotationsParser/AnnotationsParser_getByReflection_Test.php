<?php

use Orm\AnnotationsParser;

/**
 * @covers Orm\AnnotationsParser::getByReflection
 */
class AnnotationsParser_getByReflection_Test extends TestCase
{

	public function testCustomImplementation()
	{
		$f = function ($r) { return 'a_' . $r->getName(); };
		$p = new AnnotationsParser($f);
		$this->assertSame('a_AnnotationsParser_getByReflection_Test', $p->getByReflection(new ReflectionClass($this)));
	}

	public function testAutoDetectNette()
	{
		$p = new AnnotationsParser;
		$this->assertSame(array('covers' => array('Orm\AnnotationsParser::getByReflection')), $p->getByReflection(new ReflectionClass($this)));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationsParser', 'getByReflection');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
