<?php

use Orm\RepositoryContainer;
use Orm\ValueEntityFragment;
use Orm\Entity;
use Orm\Orm;

/**
 * @covers Orm\ValueEntityFragment::__callStatic
 */
class ValueEntityFragment_callStatic_Test extends TestCase
{

	public function test1()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestSkipped('php 5.2 (__callStatic)');
		}
		$this->setExpectedException('Orm\MemberAccessException', Orm::PACKAGE === '5.2' ? 'Call to undefined static method Orm\Object::foo().' : 'Call to undefined static method Orm\ValueEntityFragment::foo().');
		ValueEntityFragment::foo('a');
	}

	public function test2()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestSkipped('php 5.2 (__callStatic)');
		}
		$this->setExpectedException('Orm\MemberAccessException', Orm::PACKAGE === '5.2' ? 'Call to undefined static method Orm\Object::foo().' : 'Call to undefined static method Orm\Entity::foo().');
		Entity::foo('a');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__callStatic');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
