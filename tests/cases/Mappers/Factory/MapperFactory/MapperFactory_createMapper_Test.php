<?php

use Orm\MapperFactory;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MapperFactory::createMapper
 */
class MapperFactory_createMapper_Test extends TestCase
{
	private $p;
	private $f;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser_get_AnnotationClassParser;
		$this->f = new MapperFactory($this->p);
	}

	public function test()
	{
		$this->p->a['MapperFactory_createMapper_Repository'] = array('mapper' => array('ArrayObject'));
		$r = new MapperFactory_createMapper_Repository(new RepositoryContainer);
		$m = $this->f->createMapper($r);
		$this->assertInstanceOf('ArrayObject', $m);
		$this->assertSame((array) $r, (array) $m); // ArrayObject v constructoru pretypuje repository na array a obsahuje vsechny jeho property
	}

	public function testOwnGetMapperClass()
	{
		$r = new MapperFactory_createMapper_Repository(new RepositoryContainer);
		$mf = new MapperFactory_createMapper_MapperFactory(new AnnotationClassParser_get_AnnotationClassParser);
		$mf->mc = 'ArrayObject';
		$this->assertInstanceOf('ArrayObject', $mf->createMapper($r));
		$mf->mc = 'Orm\DibiMapper';
		$this->assertInstanceOf('Orm\DibiMapper', $mf->createMapper($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperFactory', 'createMapper');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
