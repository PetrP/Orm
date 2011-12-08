<?php

use Orm\ArrayManyToManyMapper;
use Orm\Callback;

/**
 * @covers Orm\DibiPersistenceHelper::scalarizeValue
 */
class DibiPersistenceHelper_scalarizeValue_Test extends DibiPersistenceHelper_Test
{

	public function testInt()
	{
		$r = $this->h->call('scalarizeValue', array(563, 'miXed', $this->e));
		$this->assertSame(563, $r);
	}

	public function testFloat()
	{
		$r = $this->h->call('scalarizeValue', array(1.4556, 'miXed', $this->e));
		$this->assertSame(1.4556, $r);
	}

	public function testString()
	{
		$r = $this->h->call('scalarizeValue', array('gfvdcsxfgh', 'miXed', $this->e));
		$this->assertSame('gfvdcsxfgh', $r);
	}

	public function testStringEmpty()
	{
		$r = $this->h->call('scalarizeValue', array('', 'miXed', $this->e));
		$this->assertSame('', $r);
	}

	public function testToString()
	{
		$h = Callback::create($this, 'testToString');;
		$r = $this->h->call('scalarizeValue', array($h, 'miXed', $this->e));
		$this->assertSame('DibiPersistenceHelper_scalarizeValue_Test::testToString', $r);
	}

	public function testBool()
	{
		$r = $this->h->call('scalarizeValue', array(true, 'miXed', $this->e));
		$this->assertSame(true, $r);
		$r = $this->h->call('scalarizeValue', array(false, 'miXed', $this->e));
		$this->assertSame(false, $r);
	}

	public function testNull()
	{
		$r = $this->h->call('scalarizeValue', array(NULL, 'miXed', $this->e));
		$this->assertSame(NULL, $r);
	}

	public function testArray()
	{
		$r = $this->h->call('scalarizeValue', array(array(), 'miXed', $this->e));
		$this->assertSame('a:0:{}', $r);
		$r = $this->h->call('scalarizeValue', array(array(1 => true, 'asd' => 'abc'), 'miXed', $this->e));
		$this->assertSame('a:2:{i:1;b:1;s:3:"asd";s:3:"abc";}', $r);
	}

	public function testArrayObject()
	{
		$r = $this->h->call('scalarizeValue', array(new ArrayObject(array('cow', 'boy')), 'miXed', $this->e));
		if (PHP_VERSION_ID < 50300)
		{
			$s = 'O:11:"ArrayObject":2:{i:0;s:3:"cow";i:1;s:3:"boy";}';
		}
		else
		{
			$s = 'C:11:"ArrayObject":49:{x:i:0;a:2:{i:0;s:3:"cow";i:1;s:3:"boy";};m:a:0:{}}';
		}
		$this->assertSame($s, $r);
	}

	public function testMyArrayObject()
	{
		$this->setExpectedException('Orm\MapperPersistenceException', "DibiPersistenceHelper_DibiPersistenceHelper: can't persist DibiPersistenceHelper_Entity::\$miXed; it contains 'MyArrayObject'.");
		$this->h->call('scalarizeValue', array(new MyArrayObject(array(1 => true, 'asd' => 'abc')), 'miXed', $this->e));
	}

	public function testEntity()
	{
		$r = $this->h->call('scalarizeValue', array($this->model->tests->getById(2), 'miXed', $this->e));
		$this->assertSame(2, $r);
	}

	public function testEntityNotPersist()
	{
		$this->setExpectedException('Orm\EntityNotPersistedException', 'DibiPersistenceHelper_Entity is not persisted');
		$this->h->call('scalarizeValue', array(new DibiPersistenceHelper_Entity, 'miXed', $this->e));
	}

	public function testEntityInjection()
	{
		$m = new DibiPersistenceHelper_scalarizeValue_Injection;
		$m->setInjectedValue(array(1,2,3));
		$r = $this->h->call('scalarizeValue', array($m, 'miXed', $this->e));
		$this->assertSame('a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}', $r);
	}

	public function testDateTime()
	{
		$d = new DateTime('2011-11-11');
		$r = $this->h->call('scalarizeValue', array($d, 'miXed', $this->e));
		$this->assertSame($d, $r);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\MapperPersistenceException', "DibiPersistenceHelper_DibiPersistenceHelper: can't persist DibiPersistenceHelper_Entity::\$miXed; it contains 'stdClass'.");
		$this->h->call('scalarizeValue', array((object) array(), 'miXed', $this->e));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'scalarizeValue');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
