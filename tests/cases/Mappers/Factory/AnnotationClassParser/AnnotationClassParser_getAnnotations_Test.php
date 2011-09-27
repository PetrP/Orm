<?php

use Orm\AnnotationsParser;

/**
 * @covers Orm\AnnotationClassParser::getAnnotations
 */
class AnnotationClassParser_getAnnotations_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser_getAnnotations_AnnotationClassParser(new AnnotationsParser);
	}

	public function test()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\AnnotationClassParser::getAnnotations() is deprecated; use Orm\AnnotationClassParser->parser->getByReflection() instead.');
		$this->p->_getAnnotations(new ReflectionClass('Nette\Reflection\AnnotationsParser'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationClassParser', 'getAnnotations');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
