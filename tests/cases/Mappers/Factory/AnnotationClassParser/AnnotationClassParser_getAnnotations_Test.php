<?php

/**
 * @covers Orm\AnnotationClassParser::getAnnotations
 */
class AnnotationClassParser_getAnnotations_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser_getAnnotations_AnnotationClassParser;
	}

	public function test()
	{
		$a = $this->p->_getAnnotations(new ReflectionClass('Nette\Reflection\AnnotationsParser'));
		$this->assertSame(3, count($a));
		$this->assertSame(array('Annotations support for PHP.'), $a['description']);
		$this->assertSame(array('David Grudl'), $a['author']);
		$this->assertSame(array(true), $a['Annotation']);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationClassParser', 'getAnnotations');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
