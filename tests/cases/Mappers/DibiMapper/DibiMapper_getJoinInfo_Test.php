<?php

use Orm\RepositoryContainer;
use Orm\DibiMapper;

/**
 * @covers Orm\DibiMapper::getJoinInfo
 * @covers Orm\DibiJoinHelper
 * @see DibiCollection_join_ManyToMany_Test
 * @see DibiCollection_join_OneToMany_Test
 * @see DibiCollection_join_Test
 */
class DibiMapper_getJoinInfo_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new DibiMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testNoArrow()
	{
		$this->assertNull($this->m->getJoinInfo('blaBla'));
	}

	public function testNoFk()
	{
		$this->setExpectedException('Orm\MapperJoinException', 'TestsRepository: has no joinable relationship on `string`. It is not possible to execute join.');
		$this->m->getJoinInfo('string->id');
	}

	public function testProperFormat()
	{
		$model = new RepositoryContainer;
		$model->DibiMapper_getJoinInfo1_ManyToMany_->mapper->getJoinInfo('joins->name');
		$c1 = $model->DibiMapper_getJoinInfo1_ManyToMany_->mapper->getConventional();
		$c2 = $model->DibiMapper_getJoinInfo2_ManyToMany_->mapper->getConventional();

		$this->assertSame(array('storageFormat' => array(
			'join',
		)), $c1->info);
		$this->assertSame(array('storageFormat' => array(
			'name',
		)), $c2->info);
	}

	private function primaryKey($mode, $p1, $p2, & $m1, & $m2, $join = 'joins->name')
	{
		$model = new RepositoryContainer;
		$m1 = $model->{"DibiMapper_getJoinInfo1_{$mode}_"}->mapper;
		$m2 = $model->{"DibiMapper_getJoinInfo2_{$mode}_"}->mapper;

		if ($p1)
		{
			$m1->c = new SqlConventional_getPrimaryKey_SqlConventional($m1);
		}
		if ($p2)
		{
			$m2->c = new SqlConventional_getPrimaryKey_SqlConventional($m2);
		}
		return (array) $m1->getJoinInfo($join);
	}

	/**
	 * @dataProvider dataProviderPrimaryKeyManyToMany
	 */
	public function testPrimaryKeyManyToMany($p1, $p2, $k1, $k2)
	{
		$x = $this->primaryKey('ManyToMany', $p1, $p2, $m1, $m2);
		$this->assertSame(array(
			'key' => 'joins.name',
			'joins' => array(
				0 => array(
					'alias' => 'm2m__joins',
					'xConventionalKey' => $k1,
					'yConventionalKey' => 'parent_id',
					'table' => 'mm',
					'findBy' => array(),
				),
				1 => array(
					'mapper' => $m2,
					'conventional' => $m2->conventional,
					'table' => 'dibimapper_getjoininfo2_manytomany_',
					'sourceKey' => 'joins',
					'xConventionalKey' => 'child_id',
					'yConventionalKey' => $k2,
					'alias' => 'joins',
					'findBy' => array(),
				),
			),
		), $x);
	}

	public function dataProviderPrimaryKeyManyToMany()
	{
		return array(
			'Parent' => array(true, false, 'foo_bar', 'id'),
			'Child' => array(false, true, 'id', 'foo_bar'),
			'Both' => array(true, true, 'foo_bar', 'foo_bar'),
			'None' => array(false, false, 'id', 'id'),
		);
	}

	/**
	 * @dataProvider dataProviderPrimaryKeyOneToMany
	 */
	public function testPrimaryKeyOneToMany($p1, $p2, $k1)
	{
		$x = $this->primaryKey('OneToMany', $p1, $p2, $m1, $m2);
		$this->assertSame(array(
			'key' => 'joins.name',
			'joins' => array(
				0 => array(
					'mapper' => $m2,
					'conventional' => $m2->conventional,
					'table' => 'dibimapper_getjoininfo2_onetomany_',
					'sourceKey' => 'joins',
					'xConventionalKey' => $k1,
					'yConventionalKey' => 'join_id',
					'alias' => 'joins',
					'findBy' => array(),
				),
			),
		), $x);
	}

	public function dataProviderPrimaryKeyOneToMany()
	{
		return array(
			'Parent' => array(true, false, 'foo_bar'),
			'Child' => array(false, true, 'id'),
			'Both' => array(true, true, 'foo_bar'),
			'None' => array(false, false, 'id'),
		);
	}

	/**
	 * @dataProvider dataProviderPrimaryKeyManyToOne
	 */
	public function testPrimaryKeyManyToOne($p1, $p2, $k1)
	{
		$x = $this->primaryKey('OneToMany', $p1, $p2, $m1, $m2, 'join->name');
		$this->assertSame(array(
			'key' => 'join.name',
			'joins' => array(
				0 => array(
					'mapper' => $m2,
					'conventional' => $m2->conventional,
					'table' => 'dibimapper_getjoininfo2_onetomany_',
					'sourceKey' => 'join',
					'xConventionalKey' => 'join_id',
					'yConventionalKey' => $k1,
					'alias' => 'join',
					'findBy' => array(),
				),
			),
		), $x);
	}

	public function dataProviderPrimaryKeyManyToOne()
	{
		return array(
			'Parent' => array(true, false, 'id'),
			'Child' => array(false, true, 'foo_bar'),
			'Both' => array(true, true, 'foo_bar'),
			'None' => array(false, false, 'id'),
		);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getJoinInfo');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
