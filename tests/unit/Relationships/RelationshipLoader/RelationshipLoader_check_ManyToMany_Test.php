<?php

use Nette\InvalidStateException;
use Orm\MetaData;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\RelationshipLoader::check
 * @covers Orm\MetaDataProperty::toArray
 * @covers Orm\RelationshipLoader::__construct
 */
class RelationshipLoader_check_ManyToMany_Test extends TestCase
{
	private function t($param)
	{
		MetaData::clean();
		RelationshipLoader_ManyToMany1_Entity::$param = $param;
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity', new RepositoryContainer);
		if (isset($many['manyX'])) return $many['manyX']['relationshipParam'];
		return $many['many']['relationshipParam'];
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_::$unexist neni asociace ktera by ukazovala zpet');
		$this->t('unexist');
	}

	public function testNotRelationship()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$id neni asociace ktera by ukazovala zpet');
		$this->t('id');
	}

	public function testParamEmpty()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyEmptyParam neni vyplnen param ktery by ukazoval zpet');
		$this->t('manyEmptyParam');
	}

	public function testAnotherParam()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherParam neukazuje zpet; ukazuje na jiny parametr (manyAnotherParam)');
		$this->t('manyAnotherParam');
	}

	public function testAnotherRepo()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
		$this->t('manyAnotherRepo');
	}

	public function testOk()
	{
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
	}

	public function testClearCache()
	{
		try {
			$this->t('manyAnotherRepo');
			throw new Exception;
		} catch (InvalidStateException $e) {
			$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
			$this->t('manyAnotherRepo');
		}
	}

	public function testIgnoreCase()
	{
		$ecn = & RelationshipLoader_ManyToMany2_Repository::$ecn;
		$old = $ecn;
		$ecn = strtoupper($ecn);
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
		$ecn = $old;
	}

	public function testMultiUse()
	{
		MetaData::clean();
		new RepositoryContainer;
		MetaData::getEntityRules('RelationshipLoader_ManyToMany4a_Entity');
		$this->assertTrue(true);
	}

}
