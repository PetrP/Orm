<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::createMapper
 */
class Repository_createMapper_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r = new TestsRepository($this->m);
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
	}

	public function testDefault()
	{
		$r = new Repository_DefaultMapper_Repository($this->m);
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'Repository_DefaultMapper_Repository::@mapper no class found');
		$r->getMapper();
	}

	public function testNamespace()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = 'Repository_createMapper\Repository_createMapperRepository'; // aby nebyl parse error v php52
		$r = new $c($this->m);
		$this->assertInstanceOf('Repository_createMapper\Repository_createMapperMapper', $r->getMapper());
	}

	public function testFactory()
	{
		$factory = new Repository_createMapper_MapperFactory;
		$this->m->getContext()->removeService('mapperFactory')->addService('mapperFactory', $factory);
		$r = new Repository_DefaultMapper_Repository($this->m);
		$factory->class = new TestsMapper($r);
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
	}

	public function testFactoryBadFactory()
	{
		$this->m->getContext()->removeService('mapperFactory')->addService('mapperFactory', new ArrayObject);
		$r = new Repository_DefaultMapper_Repository($this->m);
		$this->setExpectedException('Orm\ServiceNotInstanceOfException', "Service 'mapperFactory' is not instance of 'Orm\\IMapperFactory'.");
		$r->getMapper();
	}

	public function testFactoryBadReturn()
	{
		$factory = new Repository_createMapper_MapperFactory;
		$this->m->getContext()->removeService('mapperFactory')->addService('mapperFactory', $factory);
		$r = new Repository_DefaultMapper_Repository($this->m);
		$factory->class = '';
		$this->setExpectedException('Orm\BadReturnException', "Repository_DefaultMapper_Repository::createMapper() must return Orm\\IMapper, 'string' given");
		$r->getMapper();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'createMapper');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
