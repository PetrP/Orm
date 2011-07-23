<?php

/**
 * @covers Orm\AttachableEntityFragment::getGeneratingRepository
 */
class AttachableEntityFragment_getGeneratingRepository_Test extends TestCase
{

	public function test()
	{
		$e = new TestEntity;
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\Entity::getGeneratingRepository() is deprecated; use Orm\Entity::getRepository() instead');
		$e->getGeneratingRepository();
	}

}
