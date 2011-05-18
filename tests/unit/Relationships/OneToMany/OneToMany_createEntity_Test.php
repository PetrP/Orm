<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::createEntity
 * @covers BaseToMany::createEntity
 */
class OneToMany_createEntity_Test extends OneToMany_Test
{
	private function tt($enter)
	{
		$e = $this->o2m->add($enter);
		$this->o2m->remove($e);
		return $e;
	}

	public function testEntity()
	{
		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->tt($e));
		$this->assertSame($this->r, $e->getGeneratingRepository());
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
		$this->assertSame($this->r, $e->getGeneratingRepository());
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
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testArrayWithId()
	{
		$e = $this->r->getById(11);
		$ee = $this->tt(array('id' => 11, 'string' => 'xyz'));
		$this->assertSame($e, $ee);
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testArrayWithIdNotFound()
	{
		$e = $this->tt(array('id' => 333, 'string' => 'xyz'));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testTraversable()
	{
		$e = $this->tt(new DibiRow(array('string' => 'xyz')));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testWipeGet()
	{
		$this->o2m->get();
		$this->assertAttributeInstanceOf('IEntityCollection', 'get', $this->o2m);
		$this->tt(11);
		$this->assertAttributeSame(NULL, 'get', $this->o2m);
	}

}
