<?php

use Orm\EntityToArray;
use Orm\RepositoryContainer;

/**
 * @covers Orm\EntityToArray::toArray
 * @covers Orm\BaseEntityFragment::toArray
 */
class EntityToArray_toArray_Test extends TestCase
{
	private $r;
	private $r2;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntityRepository;
		$this->r2 = $m->{'EntityToArray_toArray_1m_Repository'};
	}

	public function testEmpty()
	{
		$e = new TestEntity;
		$this->assertSame(array(
			'id' => NULL,
			'string' => '',
			'date' => $e->date,
		), $e->toArray());
	}

	public function testSets()
	{
		$e = new TestEntity;
		$e->string = 'blabla';
		$e->date = '+1 year';
		$this->assertSame(array(
			'id' => NULL,
			'string' => 'blabla',
			'date' => $e->date,
		), $e->toArray());

		$e->string = 'asdasd';
		$e->date = '-1 year';
		$this->assertSame(array(
			'id' => NULL,
			'string' => 'asdasd',
			'date' => $e->date,
		), $e->toArray());
	}

	public function testWithId()
	{
		$e = $this->r->getById(1);
		$this->assertSame(array(
			'id' => 1,
			'string' => 'string',
			'date' => $e->date,
		), $e->toArray());
	}

	public function testEntityAsIs()
	{
		$e = new EntityToArray_toArray_m1_Entity;
		$e->e = $this->r->getById(1);
		$a = $e->toArray(EntityToArray::ENTITY_AS_IS);

		$this->assertInstanceOf('Orm\IEntity', $a['e']);
		$this->assertSame($e->e, $a['e']);
	}

	public function testEntityAsId()
	{
		$e = new EntityToArray_toArray_m1_Entity;
		$e->e = $this->r->getById(1);
		$a = $e->toArray(EntityToArray::ENTITY_AS_ID);

		$this->assertSame(1, $a['e']);
	}

	public function testEntityAsArray()
	{
		$e = new EntityToArray_toArray_m1_Entity;
		$e->e = $this->r->getById(1);
		$a = $e->toArray(EntityToArray::ENTITY_AS_ARRAY);

		$this->assertSame(array(
			'id' => 1,
			'string' => 'string',
			'date' => $e->e->date,
		), $a['e']);
	}

	public function testNoEntityAs_Empty()
	{
		$e = new EntityToArray_toArray_m1_Entity;
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID);

		$this->assertSame(NULL, $a['e']);
	}

	public function testNoEntityAs()
	{
		$e = new EntityToArray_toArray_m1_Entity;
		$e->e = $this->r->getById(1);

		$this->setExpectedException('Orm\EntityToArrayNoModeException', 'EntityToArray_toArray_m1_Entity::toArray() no mode for entity; use Orm\EntityToArray::ENTITY_AS_IS, ENTITY_AS_ID or ENTITY_AS_ARRAY.');
		$e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID);
	}

	public function testRelationshipAsIs()
	{
		$e = new EntityToArray_toArray_1m_Entity;
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_IS);

		$this->assertInstanceOf('Orm\IRelationship', $a['r']);
		$this->assertSame($e->r, $a['r']);
	}

	public function testRelationshipAsArrayOfId_Empty()
	{
		$e = new EntityToArray_toArray_1m_Entity;
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID);

		$this->assertSame(array(), $a['r']);
	}

	public function testRelationshipAsArrayOfId()
	{
		$e = $this->r2->attach(new EntityToArray_toArray_1m_Entity);
		$e->r->add($this->r->getById(1));
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID);

		$this->assertSame(array(1), $a['r']);
	}

	public function testRelationshipAsArrayOfArray_Empty()
	{
		$e = new EntityToArray_toArray_1m_Entity;
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ARRAY);

		$this->assertSame(array(), $a['r']);
	}

	public function testRelationshipAsArrayOfArray()
	{
		$e = $this->r2->attach(new EntityToArray_toArray_1m_Entity);
		$e->r->add($ee = $this->r->getById(1));
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ARRAY);

		$this->assertSame(array(array(
			'id' => 1,
			'string' => 'string',
			'date' => $ee->date,
		)), $a['r']);
	}

	public function testNoRelationshipAs_Empty()
	{
		$e = new EntityToArray_toArray_1m_Entity;
		$a = $e->toArray(EntityToArray::ENTITY_AS_IS);

		$this->assertSame(array(), $a['r']);
	}

	public function testNoRelationshipAs()
	{
		$e = $this->r2->attach(new EntityToArray_toArray_1m_Entity);
		$e->r->add($ee = $this->r->getById(1));

		$this->setExpectedException('Orm\EntityToArrayNoModeException', 'EntityToArray_toArray_1m_Entity::toArray() no mode for entity; use Orm\EntityToArray::RELATIONSHIP_AS_IS, RELATIONSHIP_AS_ARRAY_OF_ID or RELATIONSHIP_AS_ARRAY_OF_ARRAY.');
		$e->toArray(EntityToArray::ENTITY_AS_IS);
	}

	public function testConstants()
	{
		$this->assertBitAnd(EntityToArray::ENTITY_AS_IS, EntityToArray::AS_IS);
		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_ID, EntityToArray::AS_IS);
		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_ARRAY, EntityToArray::AS_IS);
		$this->assertBitAnd(EntityToArray::RELATIONSHIP_AS_IS, EntityToArray::AS_IS);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID, EntityToArray::AS_IS);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ARRAY, EntityToArray::AS_IS);

		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_IS, EntityToArray::AS_ID);
		$this->assertBitAnd(EntityToArray::ENTITY_AS_ID, EntityToArray::AS_ID);
		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_ARRAY, EntityToArray::AS_ID);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_IS, EntityToArray::AS_ID);
		$this->assertBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID, EntityToArray::AS_ID);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ARRAY, EntityToArray::AS_ID);

		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_IS, EntityToArray::AS_ARRAY);
		$this->assertNotBitAnd(EntityToArray::ENTITY_AS_ID, EntityToArray::AS_ARRAY);
		$this->assertBitAnd(EntityToArray::ENTITY_AS_ARRAY, EntityToArray::AS_ARRAY);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_IS, EntityToArray::AS_ARRAY);
		$this->assertNotBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID, EntityToArray::AS_ARRAY);
		$this->assertBitAnd(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ARRAY, EntityToArray::AS_ARRAY);
	}

	public function assertBitAnd($expected, $bits)
	{
		$this->assertSame($expected, $bits & $expected);
	}
	public function assertNotBitAnd($expected, $bits)
	{
		$this->assertSame(0, $bits & $expected);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EntityToArray', 'toArray');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
