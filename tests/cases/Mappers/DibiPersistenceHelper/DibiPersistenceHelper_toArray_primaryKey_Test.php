<?php

/**
 * @covers Orm\DibiPersistenceHelper::toArray
 */
class DibiPersistenceHelper_toArray_primaryKey_Test extends DibiPersistenceHelper_Test
{

	public function testAll()
	{
		$this->h->primaryKey = 'foo_bar';
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsIdNotWork()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->params['id'] = function () { throw new Exception(); };
		$r = $this->h->call('toArray', array($this->e, NULL, 'insert'));
		$this->assertSame(array(
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testParamsIdNotWorkFooBar()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->params['foo_bar'] = function () { throw new Exception(); };
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, NULL, 'insert'));
	}

	public function testParamsId()
	{
		$this->h->primaryKey = 'foo_bar';
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'foo_bar' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWhichParamsId()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->whichParams = array('id');
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'foo_bar' => 35,
		), $r);
	}

	public function testWhichParamsIdFooBar()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->whichParams = array('foo_bar');
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, 35, 'insert'));
	}

	public function testWhichParamsIdNot()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->whichParams = array();
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'foo_bar' => 35,
		), $r);
	}

	public function testWhichParamsNotId()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->whichParamsNot = array('id');
		$r = $this->h->call('toArray', array($this->e, 35, 'insert'));
		$this->assertSame(array(
			'foo_bar' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWhichParamsNotIdFooBar()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->whichParamsNot = array('foo_bar');
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, 35, 'insert'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'toArray');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
