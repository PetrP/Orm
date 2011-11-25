<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\DibiPersistenceHelper::toArray
 */
class DibiPersistenceHelper_toArray_Test extends DibiPersistenceHelper_Test
{

	public function testAll()
	{
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	/**
	 * @dataProvider dataProviderCallbacks
	 */
	public function testParamsCallback($callback)
	{
		$this->h->params['miXed2'] = $callback;
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 'foo_2_3',
			'mi_xed' => 1,
			'mi_xed3' => 3,
		), $r);
	}

	public function dataProviderCallbacks()
	{
		return array(
			'closure' => array(function ($value, DibiPersistenceHelper_Entity $e) {
				return 'foo_' . $value . '_' . $e->miXed3;
			}),
			'createFunction' => array(create_function('$value, DibiPersistenceHelper_Entity $e', '
				return "foo_" . $value . "_" . $e->miXed3;
			')),
			'callback' => array(callback(function ($value, DibiPersistenceHelper_Entity $e) {
				return 'foo_' . $value . '_' . $e->miXed3;
			})),
			'native' => array(array($this, 'callback')),
		);
	}

	public function callback($value, DibiPersistenceHelper_Entity $e)
	{
		return 'foo_' . $value . '_' . $e->miXed3;
	}

	public function testParamsBad()
	{
		$this->h->params['miXed2'] = 'foo';
		$this->setExpectedException('Orm\NotCallableException', "Callback 'foo' is not callable.");
		$this->h->call('toArray', array($this->e, NULL, 'insert'));
	}

	public function testParamsFalse()
	{
		$this->h->params['miXed2'] = false;
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsFalseUnexists()
	{
		$this->h->params['foo'] = false;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo.');
		$this->h->call('toArray', array($this->e, NULL, 'insert'));
	}

	public function testParamsTrue()
	{
		$this->h->params['miXed2'] = true;
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 2,
			'mi_xed' => 1,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsIdNotWork()
	{
		$this->h->params['id'] = function () { throw new Exception(); };
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsId()
	{
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'id' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsUnexists()
	{
		$this->h->params['foo'] = function () { throw new Exception(); };
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo.');
		$this->h->call('toArray', array($this->e, NULL, 'insert'));
	}

	public function testParamsUnexistsGetter()
	{
		$this->h->params['miXed4'] = true;
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed4' => 4,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWhichParams()
	{
		$this->h->whichParams = array('miXed', 'miXed3');
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWhichParamsNot()
	{
		$this->h->whichParamsNot = array('miXed', 'miXed3');
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 2,
		), $r);
	}

	public function testWhichParamsAndWhichParamsNot()
	{
		$this->h->whichParams = array('miXed', 'miXed2');
		$this->h->whichParamsNot = array('miXed');
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 2,
		), $r);
	}

	public function testWhichParamsId()
	{
		$this->h->whichParams = array('id');
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'id' => 35,
		), $r);
	}

	public function testWhichParamsIdNot()
	{
		$this->h->whichParams = array();
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'id' => 35,
		), $r);
	}

	public function testWhichParamsNotId()
	{
		$this->h->whichParamsNot = array('id');
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'id' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'toArray');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testRelationshipTrue()
	{
		$this->h->params['miXed'] = true;
		$this->h->params['miXed2'] = true;
		$this->h->params['miXed3'] = true;
		$this->ee->miXed = new Orm\OneToMany($this->ee, new RelationshipMetaDataOneToMany('DibiPersistenceHelper_Entity', 'miXed', get_class($this->ee->repository), 'foo'));
		$this->ee->miXed2 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'array', get_class($this->ee->repository), 'array', NULL, true), array(1, 2, 3));
		$this->ee->miXed3 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'dibi', get_class($this->ee->repository), 'dibi', NULL, true));
		$r = $this->h->call('toArray', array($this->ee, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 'a:3:{i:1;i:1;i:2;i:2;i:3;i:3;}',
		), $r);
	}

	public function testRelationshipFalse()
	{
		$this->h->params['miXed'] = false;
		$this->h->params['miXed2'] = false;
		$this->h->params['miXed3'] = false;
		$this->ee->miXed = new Orm\OneToMany($this->ee, new RelationshipMetaDataOneToMany('DibiPersistenceHelper_Entity', 'miXed', get_class($this->ee->repository), 'foo'));
		$this->ee->miXed2 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'array', get_class($this->ee->repository), 'array', NULL, true), array(1, 2, 3));
		$this->ee->miXed3 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'dibi', get_class($this->ee->repository), 'dibi', NULL, true));
		$r = $this->h->call('toArray', array($this->ee, NULL, 'insert'));
		$this->assertSame(array(), $r);
	}

	public function testRelationshipCallback1()
	{
		$this->h->params['miXed'] = function () { return 'x';};
		$this->h->params['miXed2'] = function () { return 'x';};
		$this->h->params['miXed3'] = function () { return 'x';};
		$this->ee->miXed = new Orm\OneToMany($this->ee, new RelationshipMetaDataOneToMany('DibiPersistenceHelper_Entity', 'miXed', get_class($this->ee->repository), 'foo'));
		$this->ee->miXed2 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'array', get_class($this->ee->repository), 'array', NULL, true), array(1, 2, 3));
		$this->ee->miXed3 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'dibi', get_class($this->ee->repository), 'dibi', NULL, true));
		$r = $this->h->call('toArray', array($this->ee, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 'x',
			'mi_xed2' => 'x',
			'mi_xed3' => 'x',
		), $r);
	}

	public function testRelationshipCallback2()
	{
		$this->h->params['miXed'] = function ($v) { return $v;};
		$this->h->params['miXed2'] = function ($v) { return $v;};
		$this->h->params['miXed3'] = function ($v) { return $v;};
		$this->ee->miXed = new Orm\OneToMany($this->ee, new RelationshipMetaDataOneToMany('DibiPersistenceHelper_Entity', 'miXed', get_class($this->ee->repository), 'foo'));
		$this->ee->miXed2 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'array', get_class($this->ee->repository), 'array', NULL, true), array(1, 2, 3));
		$this->ee->miXed3 = new Orm\ManyToMany($this->ee, new RelationshipMetaDataManyToMany('DibiPersistenceHelper_Entity', 'dibi', get_class($this->ee->repository), 'dibi', NULL, true));
		$r = $this->h->call('toArray', array($this->ee, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed2' => 'a:3:{i:1;i:1;i:2;i:2;i:3;i:3;}',
		), $r);
	}
}
