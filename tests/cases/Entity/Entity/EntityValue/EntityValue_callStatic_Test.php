<?php

use Orm\RepositoryContainer;
use Orm\_EntityValue;
use Orm\Entity;
use Orm\Orm;

/**
 * @covers Orm\_EntityValue::__callStatic
 */
class EntityValue_callStatic_Test extends TestCase
{

	public function test1()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestSkipped('php 5.2 (__callStatic)');
		}
		$this->setExpectedException('Nette\MemberAccessException', Orm::PACKAGE === '5.2' ? 'Call to undefined static method Orm\Object::foo().' : 'Call to undefined static method Orm\_EntityValue::foo().');
		_EntityValue::foo('a');
	}

	public function test2()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestSkipped('php 5.2 (__callStatic)');
		}
		$this->setExpectedException('Nette\MemberAccessException', Orm::PACKAGE === '5.2' ? 'Call to undefined static method Orm\Object::foo().' : 'Call to undefined static method Orm\Entity::foo().');
		Entity::foo('a');
	}

}
