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

}
