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

	public function testRepositoryHelper()
	{
		$this->assertInstanceOf('Orm\RepositoryHelper', $this->c->getService('repositoryHelper'));
	}

	public function testDibiNoConnection()
	{
		$this->setExpectedException('DibiException', 'Dibi is not connected to database.');
		$this->c->getService('dibi');
	}

	public function testDibiHasConnection()
	{
		$r = new ReflectionProperty('Dibi', 'connection');
		setAccessible($r);

		Dibi::setConnection($c = new DibiConnection(array('lazy' => true)));
		$this->assertInstanceOf('DibiConnection', $this->c->getService('dibi'));
		$this->assertSame($c, $this->c->getService('dibi'));

		$r->setValue(NULL);
	}
}
