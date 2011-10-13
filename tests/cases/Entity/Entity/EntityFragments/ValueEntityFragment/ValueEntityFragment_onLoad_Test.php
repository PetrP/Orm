<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ValueEntityFragment::onLoad
 */
class ValueEntityFragment_onLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntityRepository;
	}

	public function test()
	{
		$e = $this->r->getById(1);
		$this->assertSame(array('id' => true), $this->readAttribute($e, 'valid'));
		$this->assertInternalType('array', $this->readAttribute($e, 'values'));
		$this->assertSame(false, $e->isChanged());
		$this->assertInternalType('array', $this->readAttribute($e, 'rules'));
	}

	public function test2()
	{
		$e = new TestEntity;
		$e->fireEvent('onLoad', $this->r, array('xxx' => 'yyy', 'id' => 1));
		$this->assertSame(array('xxx' => 'yyy', 'id' => 1), $this->readAttribute($e, 'values'));
	}

	public function testBadId()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\NotValidException', "Param TestEntity::\$id must be 'id'; '0' given");
		$e->fireEvent('onLoad', $this->r, array('id' => 0));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onLoad');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
