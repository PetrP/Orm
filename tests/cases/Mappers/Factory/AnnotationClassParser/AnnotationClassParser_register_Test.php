<?php

use Orm\AnnotationClassParser;
use Orm\AnnotationsParser;

/**
 * @covers Orm\AnnotationClassParser::register
 */
class AnnotationClassParser_register_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser(new AnnotationsParser);
	}

	private function t($annotation, $interface, $defaultClassFallback)
	{
		$this->p->register($annotation, $interface, $defaultClassFallback);
		$r = $this->readAttribute($this->p, 'registered');
		$this->assertArrayHasKey($annotation, $r);
		$r = (array) $r[$annotation];
		$this->assertSame(4, count($r));
		$this->assertSame($annotation, $r['annotation']);
		$this->assertSame($interface, $r['interface']);
		$this->assertSame($defaultClassFallback, $r['defaultClassFallback']);
		$this->assertSame(array(), $r['cache']);
	}

	public function callback()
	{

	}

	public function testNoFallback()
	{
		$this->t('test', 'Countable', NULL);
	}

	public function testFallbackFunction()
	{
		$this->t('test', 'Countable', function () {});
	}

	public function testFallbackCallback()
	{
		$this->t('test', 'Countable', callback(function () {}));
	}

	public function testFallbackCreateFunction()
	{
		$this->t('test', 'Countable', create_function('', ''));
	}

	public function testFallbackArray()
	{
		$this->t('test', 'Countable', array($this, 'callback'));
	}

	public function testAlreadyRegistered()
	{
		$this->t('test', 'Countable', NULL);
		$this->setExpectedException('Orm\AnnotationClassParserException', "Parser 'test' is already registered");
		$this->t('test', 'Countable', NULL);
	}

	public function testNoInterface1()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'' is not valid interface");
		$this->t('test', NULL, NULL);
	}

	public function testNoInterface2()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'Directory' is not valid interface");
		$this->t('test', 'Directory', NULL);
	}

	public function testNoInterface3()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'Directory' is not valid interface");
		$this->t('test', 'Directory', NULL);
	}

	public function testNoValidCallback1()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'foo' is not valid callback");
		$this->t('test', 'Countable', 'foo');
	}

	public function testNoValidCallback2()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'ArrayObject' is not valid callback");
		$this->t('test', 'Countable', new ArrayObject);
	}

	public function testNoValidCallback3()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "'array' is not valid callback");
		$this->t('test', 'Countable', array());
	}

	public function testReturns()
	{
		$this->assertSame($this->p, $this->p->register('test', 'Countable'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationClassParser', 'register');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
