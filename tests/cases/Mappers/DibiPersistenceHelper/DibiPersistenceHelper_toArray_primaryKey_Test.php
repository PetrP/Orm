<?php

/**
 * @covers Orm\DibiPersistenceHelper::toArray
 */
class DibiPersistenceHelper_toArray_primaryKey_Test extends DibiPersistenceHelper_Test
{

	public function testAll()
	{
		$this->h->primaryKey = 'foo_bar';
		$r = $this->h->call('toArray', array($this->e, NULL));
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
		$r = $this->h->call('toArray', array($this->e, NULL));
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
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, NULL));
	}

	public function testParamsId()
	{
		$this->h->primaryKey = 'foo_bar';
		$r = $this->h->call('toArray', array($this->e, 35));
		$this->assertSame(array(
			'foo_bar' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWitchParamsId()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->witchParams = array('id');
		$r = $this->h->call('toArray', array($this->e, 35));
		$this->assertSame(array(
			'foo_bar' => 35,
		), $r);
	}

	public function testWitchParamsIdFooBar()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->witchParams = array('foo_bar');
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, 35));
	}

	public function testWitchParamsIdNot()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->witchParams = array();
		$r = $this->h->call('toArray', array($this->e, 35));
		$this->assertSame(array(
			'foo_bar' => 35,
		), $r);
	}

	public function testWitchParamsNotId()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->witchParamsNot = array('id');
		$r = $this->h->call('toArray', array($this->e, 35));
		$this->assertSame(array(
			'foo_bar' => 35,
			'mi_xed' => 1,
			'mi_xed2' => 2,
			'mi_xed3' => 3,
		), $r);
	}

	public function testWitchParamsNotIdFooBar()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->h->witchParamsNot = array('foo_bar');
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property DibiPersistenceHelper_Entity::$foo_bar.');
		$this->h->call('toArray', array($this->e, 35));
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
