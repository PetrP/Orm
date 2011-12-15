<?php

use Orm\RepositoryContainer;
use Orm\MetaData;

/**
 * @covers Orm\ArrayManyToManyMapper::load
 */
class ArrayManyToManyMapper_load_Test extends TestCase
{

	public function testMappedHere()
	{
		$orm = new RepositoryContainer;
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', $orm);

		$r1 = $orm->{'RelationshipMetaDataManyToMany_ManyToMany1_Repository'};
		$r2 = $orm->{'RelationshipMetaDataManyToMany_ManyToMany2_Repository'};
		$m = $many['many']['relationshipParam']->getMapper($r1);

		$e1 = $r1->getById(1);
		$e21 = $r2->getById(1);
		$e22 = $r2->getById(2);
		$e21->many->add($e1);
		$e22->many->add($e1);
		$r2->persist($e21);
		$r2->persist($e22);

		$this->assertSame(array(1 => 1, 2 => 2), $m->load($e1, array(1 => 1, 2 => 2)));
		$this->assertSame(array(1 => 1, 2 => 2), $e1->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e21->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e22->many->get()->fetchPairs('id', 'id'));
	}

	public function testMappedThere()
	{
		$orm = new RepositoryContainer;
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany2_Entity', $orm);

		$r1 = $orm->{'RelationshipMetaDataManyToMany_ManyToMany1_Repository'};
		$r2 = $orm->{'RelationshipMetaDataManyToMany_ManyToMany2_Repository'};
		$m = $many['many']['relationshipParam']->getMapper($r2);

		$e11 = $r1->getById(1);
		$e12 = $r1->getById(2);
		$e2 = $r2->getById(1);
		$e11->many->add($e2);
		$e12->many->add($e2);
		$r1->persist($e11);
		$r1->persist($e12);

		$this->assertSame(array(1 => 1, 2 => 2), $m->load($e2, NULL));
		$this->assertSame(array(1 => 1, 2 => 2), $e2->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e11->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e12->many->get()->fetchPairs('id', 'id'));
	}

	public function testMappedBoth()
	{
		$orm = new RepositoryContainer;
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany6_Entity', $orm);

		$r = $orm->{'RelationshipMetaDataManyToMany_ManyToMany6_Repository'};
		$m = $many['many']['relationshipParam']->getMapper($r);

		$e1 = $r->getById(1);
		$e2 = $r->getById(2);
		$e3 = new RelationshipMetaDataManyToMany_ManyToMany6_Entity;
		$e1->many->add($e2);
		$e1->many->add($e3);
		$r->persist($e1);
		$r->persist($e2);
		$r->persist($e3);

		$this->assertSame(array(44 => 44, 2 => 2, 3 => 3), $m->load($e1, array(44 => 44)));
		$this->assertSame(array(2 => 2, 3 => 3), $e1->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e2->many->get()->fetchPairs('id', 'id'));
		$this->assertSame(array(1 => 1), $e3->many->get()->fetchPairs('id', 'id'));
	}

}
