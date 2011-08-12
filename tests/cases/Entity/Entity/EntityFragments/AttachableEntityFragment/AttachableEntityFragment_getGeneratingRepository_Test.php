<?php

/**
 * @covers Orm\AttachableEntityFragment::getGeneratingRepository
 */
class AttachableEntityFragment_getGeneratingRepository_Test extends TestCase
{

	public function test()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Entity::getGeneratingRepository() is deprecated; use Orm\Entity::getRepository() instead');
		$e->getGeneratingRepository();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'getGeneratingRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
