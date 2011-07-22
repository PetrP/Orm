<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_context_Test extends TestCase
{
	private $m;
	private $c;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
		$this->c = $this->m->getContext();
	}

	public function testAnnotationClassParser()
	{
		$this->assertInstanceOf('Orm\AnnotationClassParser', $this->c->getService('annotationClassParser'));
	}

	public function testMapperFactory()
	{
		$this->assertInstanceOf('Orm\MapperFactory', $this->c->getService('mapperFactory'));
		$this->assertAttributeSame($this->c->getService('annotationClassParser'), 'parser', $this->c->getService('mapperFactory'));
	}

}
