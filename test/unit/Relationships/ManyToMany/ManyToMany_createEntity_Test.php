<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers ManyToMany::createEntity
 * @covers BaseToMany::createEntity
 */
class ManyToMany_createEntity_Test extends ManyToMany_Test
{
	private function tt($enter)
	{
		$e = $this->m2m->add($enter);
		$this->m2m->remove($e);
		return $e;
	}

	public function testEntity()
	{
		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->tt($e));
	}

	public function testBad()
	{
		$this->setExpectedException('UnexpectedValueException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->tt(new TestEntity);
	}

	public function testId()
	{
		$e = $this->r->getById(11);
		$this->assertSame($e, $this->tt(11));
	}

	public function testIdNotFound()
	{
		$this->setExpectedException('UnexpectedValueException', 'Entity \'333\' not found in `OneToMany_Repository`');
		$this->tt(333);
	}

	public function testArray()
	{
		$e = $this->tt(array('string' => 'xyz'));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
	}

	public function testArrayWithId()
	{
		$e = $this->r->getById(11);
		$ee = $this->tt(array('id' => 11, 'string' => 'xyz'));
		$this->assertSame($e, $ee);
		$this->assertSame('xyz', $e->string);
	}

	public function testArrayWithIdNotFound()
	{
		$e = $this->tt(array('id' => 333, 'string' => 'xyz'));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
	}

	public function testTraversable()
	{
		$e = $this->tt(new DibiRow(array('string' => 'xyz')));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
	}

	public function testWipeGet()
	{
		$this->m2m->get();
		$this->assertAttributeInstanceOf('IEntityCollection', 'get', $this->m2m);
		$this->tt(11);
		$this->assertAttributeSame(NULL, 'get', $this->m2m);
	}

}
