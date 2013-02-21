<?php

use Orm\RepositoryContainer;
use Orm\OneToMany;
use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\AttachableEntityFragment::__clone
 */
class AttachableEntityFragment_clone_Test extends TestCase
{

	public function testIsAttachedAfterClone()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('TestEntityRepository');
		$e = new TestEntity;
		$r->attach($e);
		$this->assertSame(array($e), $r->getIdentityMap()->getAllNew());
		$eClone = clone $e;
		$this->assertSame($r, $e->getRepository());
		$this->assertSame($r, $eClone->getRepository());
		$this->assertSame(array($e, $eClone), $r->getIdentityMap()->getAllNew());
	}

	public function testIsNotAttachedAfterCloneOfClonedEntityIsNotAttached()
	{
		$e = new TestEntity;
		$eClone = clone $e;
		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(NULL, $eClone->getRepository(false));
	}

	public function testErrorAfterRemoveOkIfClone()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('TestEntityRepository');
		$e = new TestEntity;
		$r->attach($e);
		$r->remove($e);

		$eClone = clone $e;
		$r->attach($eClone);

		$this->assertSame($r, $eClone->getRepository());
		$this->assertSame($m, $eClone->getModel());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', '__clone');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
