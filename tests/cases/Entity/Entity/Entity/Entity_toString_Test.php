<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Entity::__toString
 */
class Entity_toString_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function testPersisted()
	{
		$e = $this->r->getById(1);
		$this->assertSame('1', $e->__toString());
		$this->assertSame('1', (string) $e);
	}

	public function testUnpersisted()
	{
		$e = new TestEntity;
		$this->assertSame('', $e->__toString());
		$this->assertSame('', (string) $e);
	}

	public function testBadId()
	{
		$class = class_exists('Orm\ValueEntityFragment') ? 'Orm\ValueEntityFragment' : 'ValueEntityFragment';
		$len = 8 + strlen($class);
		// nejpre zkontrolovat jestli funguje unserializace
		$e = <<<EOT
O:10:"TestEntity":1:{s:$len:"\x00$class\x00values";a:1:{s:2:"id";s:3:"111";}}
EOT;
		$e = unserialize($e);
		$e->fireEvent('onCreate', $this->r);
		$this->assertSame('111', $e->__toString());
		$e = <<<EOT
O:10:"TestEntity":1:{s:$len:"\x00$class\x00values";a:1:{s:2:"id";s:3:"xyz";}}
EOT;
		$e = unserialize($e);
		$e->fireEvent('onCreate', $this->r);
		$this->assertSame('', $e->__toString());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Entity', '__toString');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
