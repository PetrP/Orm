<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers EntityToArray::toArray
 * @covers _EntityBase::toArray
 */
class EntityToArray_toArray_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntity;
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

		$this->assertInstanceOf('IEntity', $a['e']);
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

		$this->setExpectedException('InvalidStateException', 'No mode for entity');
		$e->toArray(EntityToArray::RELATIONSHIP_AS_ARRAY_OF_ID);
	}

	public function testRelationshipAsIs()
	{
		$e = new EntityToArray_toArray_1m_Entity;
		$a = $e->toArray(EntityToArray::RELATIONSHIP_AS_IS);

		$this->assertInstanceOf('IRelationship', $a['r']);
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
		$e = new EntityToArray_toArray_1m_Entity;
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
		$e = new EntityToArray_toArray_1m_Entity;
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
		$e = new EntityToArray_toArray_1m_Entity;
		$e->r->add($ee = $this->r->getById(1));

		$this->setExpectedException('InvalidStateException', 'No mode for relationship');
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
}
