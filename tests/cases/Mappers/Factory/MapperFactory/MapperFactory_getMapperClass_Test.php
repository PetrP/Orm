<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\MapperFactory::getMapperClass
 */
class MapperFactory_getMapperClass_Test extends TestCase
{
	private $p;
	private $f;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser_get_AnnotationClassParser;
		$this->f = new MapperFactory_getMapperClass_MapperFactory($this->p);
	}

	public function test1()
	{
		$this->p->a['MapperFactory_createMapper_Repository'] = array('mapper' => array('Orm\DibiMapper'));
		$r = new MapperFactory_createMapper_Repository(new RepositoryContainer);
		$this->assertSame('Orm\DibiMapper', $this->f->__getMapperClass($r));
	}

	public function test2()
	{
		$this->p->a['MapperFactory_createMapper_Repository'] = array('mapper' => array('ArrayObject'));
		$r = new MapperFactory_createMapper_Repository(new RepositoryContainer);
		$this->assertSame('ArrayObject', $this->f->__getMapperClass($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperFactory', 'getMapperClass');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
