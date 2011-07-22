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

}
