<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\RepositoryContainer;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getMapper
 */
class RelationshipMetaDataManyToMany_getMapper_Test extends TestCase
{
	private $orm;
	private $r;

	protected function setUp()
	{
		$this->orm = new RepositoryContainer;
		$this->r = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'tests', 'param', 'Orm\ManyToMany');
	}

	public function test()
	{
		$m = $this->r->getMapper($this->orm->tests);
		$this->assertInstanceOf('Orm\IManyToManyMapper', $m);
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $m);
		$this->assertAttributeSame($this->r, 'meta', $m);
	}

	public function testCache()
	{
		$m = $this->r->getMapper($this->orm->tests);
		$this->assertSame($m, $this->r->getMapper($this->orm->tests));
		$this->assertSame($m, $this->r->getMapper($this->orm->tests));
	}

	public function testCacheDifferentRepositoryContainer()
	{
		$orm2 = new RepositoryContainer;
		$this->assertNotSame($this->r->getMapper($this->orm->tests), $this->r->getMapper($orm2->tests));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getMapper');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
