<?php

use Orm\MapperFactory;
use Orm\AnnotationClassParser;
use Orm\AnnotationsParser;

/**
 * @covers Orm\MapperFactory::__construct
 */
class MapperFactory_construct_Test extends TestCase
{

	public function testImplement()
	{
		$this->assertInstanceOf('Orm\IMapperFactory', new MapperFactory(new AnnotationClassParser(new AnnotationsParser)));
	}

	public function test()
	{
		$p = new AnnotationClassParser(new AnnotationsParser);
		$this->assertArrayNotHasKey('mapper', $this->readAttribute($p, 'registered'));
		$f = new MapperFactory($p);
		$r = $this->readAttribute($p, 'registered');
		$this->assertArrayHasKey('mapper', $r);
		$r = (array) $r['mapper'];
		$this->assertSame(4, count($r));
		$this->assertSame('mapper', $r['annotation']);
		$this->assertSame('Orm\IRepository', $r['interface']);
		$this->assertSame(array($f, 'createDefaultMapperClass'), $r['defaultClassFallback']);
		$this->assertSame(array(), $r['cache']);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperFactory', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
