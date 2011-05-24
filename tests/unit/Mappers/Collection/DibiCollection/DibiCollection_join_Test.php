<?php

use Orm\DibiCollection;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::join
 * @covers Orm\DibiMapper::getJoinInfo
 * @covers Orm\DibiCollection::orderBy
 * @covers Orm\FindByHelper::dibiProcess
 */
class DibiCollection_join_Test extends TestCase
{
	/** @var DibiCollection_join1_Repository */
	private $r1;
	/** @var DibiCollection_join2_Repository */
	private $r2;
	/** @var DibiCollection */
	private $c;

	private function a($expectedSql, DibiCollection $c)
	{
		$csql = $c->__toString();
		$trimcsql = trim(preg_replace('#\s+#', ' ', $csql));
		$trimsql = trim(preg_replace('#\s+#', ' ', $expectedSql));
		$this->assertSame($trimsql, $trimcsql, "$expectedSql\n\n$csql");
	}

	protected function setUp()
	{
		$model = new RepositoryContainer;
		$this->r1 = $model->dibiCollection_join1_;
		$this->r2 = $model->dibiCollection_join2_;
		$this->c = $this->r1->mapper->findAll();
	}

	public function testNoJoin()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join1_] as e
		', $this->c);
	}

	public function testOneTable()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			GROUP BY [e].[id]
			ORDER BY [join2].[name] ASC
		', $this->c->orderBy('join2->name'));
	}

	public function testOverTwoTable()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			LEFT JOIN [dibicollection_join1_] as [join2->join1] ON [join2->join1].[id] = [join2].[join1_id]
			GROUP BY [e].[id]
			ORDER BY [join2->join1].[name] ASC
		', $this->c->orderBy('join2->join1->name'));
	}

	public function testTwoJoin()
	{
		$this->a('
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			LEFT JOIN [dibicollection_join1_] as [join2->join1] ON [join2->join1].[id] = [join2].[join1_id]
			GROUP BY [e].[id]
			ORDER BY [join2->join1].[name] ASC, [join2].[name] ASC
		', $this->c->orderBy('join2->join1->name')->orderBy('join2->name'));
	}

	public function testFindBy()
	{
		$this->a("
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			LEFT JOIN [dibicollection_join1_] as [join2->join1] ON [join2->join1].[id] = [join2].[join1_id]
			WHERE ([join2->join1].[name] = 'xyz')
			GROUP BY [e].[id]
			ORDER BY [join2].[name] ASC
		", $this->c->{'findByJoin2->join1->name'}('xyz')->orderBy('join2->name'));
	}

	public function testFindBy2()
	{
		$this->a("
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			LEFT JOIN [dibicollection_join1_] as [join2->join1] ON [join2->join1].[id] = [join2].[join1_id]
			WHERE ([join2->join1].[name] = 'xyz')
			GROUP BY [e].[id]
			ORDER BY [join2].[name] ASC
		", $this->c->orderBy('join2->name')->findBy(array('join2->join1->name' => 'xyz')));
	}

	public function testUnexistFK()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_join1_Repository: neni zadna vazba na `neexistuje`');
		$this->c->orderBy('neexistuje->name');
	}

	public function testUnexistFK2()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_join2_Repository: neni zadna vazba na `neexistuje`');
		$this->c->orderBy('join2->neexistuje->name');
	}

	public function testBadMapper()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_joinBadMapper_Repository (joinBadMapper) nepouziva Orm\DibiMapper, data nelze propojit.');
		$this->c->orderBy('join2->joinBadMapper->name');
	}

	public function testDifferentConnection()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_joinDifferentConnection_Repository (joinDifferentConnection) pouziva jiny Orm\DibiConnection nez DibiCollection_join2_Repository, data nelze propojit.');
		$this->c->orderBy('join2->joinDifferentConnection->name');
	}

	public function testModifyFindAll()
	{
		$this->a("
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join3_] as [join3] ON [join3].[id] = [e].[join3_id] AND ([join3].[type] = 'xyz')
			GROUP BY [e].[id]
			ORDER BY [join3].[name] ASC
		", $this->c->orderBy('join3->name'));
	}

	public function testModifyFindAllOverMore()
	{
		$this->a("
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join2_] as [join2] ON [join2].[id] = [e].[join2_id]
			LEFT JOIN [dibicollection_join1_] as [join2->join1] ON [join2->join1].[id] = [join2].[join1_id]
			LEFT JOIN [dibicollection_join3_] as [join2->join1->join3] ON [join2->join1->join3].[id] = [join2->join1].[join3_id] AND ([join2->join1->join3].[type] = 'xyz')
			LEFT JOIN [dibicollection_join1_] as [join2->join1->join3->join1] ON [join2->join1->join3->join1].[id] = [join2->join1->join3].[join1_id]
			GROUP BY [e].[id]
			ORDER BY [join2->join1->join3->join1].[name] ASC
		", $this->c->orderBy('join2->join1->join3->join1->name'));
	}

	public function testFindAllWithJoin()
	{
		$this->a("
			SELECT [e].* FROM [dibicollection_join1_] as e
			LEFT JOIN [dibicollection_join4_] as [join4] ON [join4].[id] = [e].[join4_id] AND ([join4->join1->join3].[type] = 'aaa') AND ([join4->join1].[type] = 'bbb')
			LEFT JOIN [dibicollection_join1_] as [join4->join1] ON [join4->join1].[id] = [join4].[join1_id]
			LEFT JOIN [dibicollection_join3_] as [join4->join1->join3] ON [join4->join1->join3].[id] = [join4->join1].[join3_id] AND ([join4->join1->join3].[type] = 'xyz')
			GROUP BY [e].[id]
			ORDER BY [join4].[name] ASC
		", $this->c->orderBy('join4->name'));
	}

	public function testFindAllBadCollection()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_joinBadCollection_Repository (joinBadCollection) nepouziva Orm\DibiCollection, data nelze propojit.');
		$this->c->orderBy('join2->joinBadCollection->name');
	}

	public function testFindAllHasWhere()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'DibiCollection_joinHasWhere_Repository (joinHasWhere) Orm\DibiCollection pouziva where(), data nelze propojit.');
		$this->c->orderBy('join2->joinHasWhere->name');
	}

}
