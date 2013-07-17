<?php

use Orm\DibiCollection;
use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiCollection::join
 * @covers Orm\DibiMapper::getJoinInfo
 * @covers Orm\DibiJoinHelper
 */
class DibiCollection_join_ManyToMany_Test extends TestCase
{
	/** @var DibiCollection_join_ManyToMany1_Repository */
	private $r1;
	/** @var DibiCollection_join_ManyToMany2_Repository */
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
		$this->r1 = $model->dibiCollection_join_ManyToMany1_;
		$this->r2 = $model->dibiCollection_join_ManyToMany2_;
		$this->c = $this->r1->mapper->findAll();
	}

	public function testOneTable()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_manytomany1_` as e
			LEFT JOIN `mm` as `m2m__joins` ON `m2m__joins`.`parent_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_manytomany2_` as `joins` ON `joins`.`id` = `m2m__joins`.`child_id`
			GROUP BY `e`.`id`
			ORDER BY `joins`.`name` ASC
		', $this->c->orderBy('joins->name'));
	}

	public function testOneTableNoParam()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_manytomany1_` as e
			LEFT JOIN `mm2` as `m2m__joinsNoParam` ON `m2m__joinsNoParam`.`parent_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_manytomany2_` as `joinsNoParam` ON `joinsNoParam`.`id` = `m2m__joinsNoParam`.`child_id`
			GROUP BY `e`.`id`
			ORDER BY `joinsNoParam`.`name` ASC
		', $this->c->orderBy('joinsNoParam->name'));
	}

	public function testOverTwoTable()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_manytomany1_` as e
			LEFT JOIN `mm` as `m2m__joins` ON `m2m__joins`.`parent_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_manytomany2_` as `joins` ON `joins`.`id` = `m2m__joins`.`child_id`
			LEFT JOIN `mm` as `joins->m2m__joins` ON `joins->m2m__joins`.`child_id` = `joins`.`id`
			LEFT JOIN `dibicollection_join_manytomany1_` as `joins->joins` ON `joins->joins`.`id` = `joins->m2m__joins`.`parent_id`
			GROUP BY `e`.`id`
			ORDER BY `joins->joins`.`name` ASC
		', $this->c->orderBy('joins->joins->name'));
	}

	public function testTwoJoin()
	{
		$this->a('
			SELECT `e`.* FROM `dibicollection_join_manytomany1_` as e
			LEFT JOIN `mm` as `m2m__joins` ON `m2m__joins`.`parent_id` = `e`.`id`
			LEFT JOIN `dibicollection_join_manytomany2_` as `joins` ON `joins`.`id` = `m2m__joins`.`child_id`
			LEFT JOIN `mm` as `joins->m2m__joins` ON `joins->m2m__joins`.`child_id` = `joins`.`id`
			LEFT JOIN `dibicollection_join_manytomany1_` as `joins->joins` ON `joins->joins`.`id` = `joins->m2m__joins`.`parent_id`
			GROUP BY `e`.`id`
			ORDER BY `joins->joins`.`name` ASC, `joins`.`name` ASC
		', $this->c->orderBy('joins->joins->name')->orderBy('joins->name'));
	}

	public function testNotExixtsParam()
	{
		$this->setExpectedException('Orm\MapperJoinException', 'DibiCollection_join_ManyToMany1_Repository: has no joinable relationship on `notExistsJoin`. It is not possible to execute join.');
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
