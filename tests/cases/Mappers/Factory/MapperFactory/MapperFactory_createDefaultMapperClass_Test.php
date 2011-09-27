<?php

use Orm\AnnotationClassParser;
use Orm\MapperFactory;
use Orm\AnnotationsParser;

/**
 * @covers Orm\MapperFactory::createDefaultMapperClass
 */
class MapperFactory_createDefaultMapperClass_Test extends TestCase
{
	private $f;

	protected function setUp()
	{
		$this->f = new MapperFactory(new AnnotationClassParser(new AnnotationsParser));
	}

	public function testNoSufix()
	{
		$this->assertSame('xxxMapper', $this->f->createDefaultMapperClass('xxx'));
		$this->assertSame('XxxMapper', $this->f->createDefaultMapperClass('Xxx'));
	}

	public function testSufix()
	{
		$this->assertSame('xxxMapper', $this->f->createDefaultMapperClass('xxxRepository'));
		$this->assertSame('XxxMapper', $this->f->createDefaultMapperClass('XxxRepository'));
		$this->assertSame('XxxMapper', $this->f->createDefaultMapperClass('Xxxrepository'));
		$this->assertSame('XxxMapper', $this->f->createDefaultMapperClass('XxxREPOSITORY'));
	}

	public function testNs()
	{
		$this->assertSame('Foo\Bar\XxxMapper', $this->f->createDefaultMapperClass('Foo\Bar\XxxRepository'));
		$this->assertSame('Foo\Bar\XxxMapper', $this->f->createDefaultMapperClass('Foo\Bar\Xxx'));
		$this->assertSame('Repository\Repository\RepositoryMapper', $this->f->createDefaultMapperClass('Repository\Repository\RepositoryRepository'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperFactory', 'createDefaultMapperClass');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
