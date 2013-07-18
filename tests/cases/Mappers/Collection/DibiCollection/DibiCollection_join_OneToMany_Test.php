<?php

use Orm\DibiCollection;
use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiCollection::join
 * @covers Orm\DibiMapper::getJoinInfo
 * @covers Orm\DibiJoinHelper
 */
class DibiCollection_join_OneToMany_Test extends TestCase
{
	/** @var DibiCollection_join_OneToMany1_Repository */
	private $r1;
	/** @var DibiCollection_join_OneToMany2_Repository */
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
		$this->r1 = $model->dibiCollection_join_OneToMany1_;
		$this->r2 = $model->dibiCollection_join_OneToMany2_;
		$this->c = $this->r1->mapper->findAll();
	}

	public function testOneTable()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_onetomany1_` as e
			LEFT JOIN `dibicollection_join_onetomany2_` as `joins` ON `joins`.`join_id` = `e`.`id`
			GROUP BY `e`.`id`
			ORDER BY `joins`.`name` ASC
		', $this->c->orderBy('joins->name'));
	}

	public function testOverTwoTable1()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_onetomany1_` as e
			LEFT JOIN `dibicollection_join_onetomany2_` as `joins` ON `joins`.`join_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_onetomany1_` as `joins->join` ON `joins->join`.`id` = `joins`.`join_id`
			GROUP BY `e`.`id`
			ORDER BY `joins->join`.`name` ASC
		', $this->c->orderBy('joins->join->name'));
	}

	public function testOverTwoTable2()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_onetomany1_` as e
			LEFT JOIN `dibicollection_join_onetomany2_` as `joins` ON `joins`.`join_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_onetomany1_` as `joins->joins` ON `joins->joins`.`join_id` = `joins`.`id`
			GROUP BY `e`.`id`
			ORDER BY `joins->joins`.`name` ASC
		', $this->c->orderBy('joins->joins->name'));
	}

	public function testTwoJoin()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_onetomany1_` as e
			LEFT JOIN `dibicollection_join_onetomany2_` as `joins` ON `joins`.`join_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_onetomany1_` as `joins->joins` ON `joins->joins`.`join_id` = `joins`.`id`
			GROUP BY `e`.`id`
			ORDER BY `joins->joins`.`name` ASC, `joins`.`name` ASC
		', $this->c->orderBy('joins->joins->name')->orderBy('joins->name'));
	}

	public function testNotExixtsParam()
	{
		$this->setExpectedException('Orm\MapperJoinException', 'DibiCollection_join_OneToMany1_Repository: has no joinable relationship on `notExistsJoin`. It is not possible to execute join.');
		$this->c->orderBy('notExistsJoin->name');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiCollection', 'join');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
