<?php

use Orm\RepositoryContainer;
use Orm\AnnotationsParser;
use Orm\MapperAutoCaller;

/**
 * @covers Orm\MapperAutoCaller::__construct
 */
class MapperAutoCaller_construct_Test extends TestCase
{
	private $m;
	private $r;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
		$this->r = $this->m->tests;
	}

	public function testNoMethod1()
	{
		$c = new MapperAutoCaller($this->r, new AnnotationsParser(function () { return array(); }));
		$this->assertAttributeSame(array(), 'methods', $c);
	}

	public function testNoMethod2()
	{
		$c = new MapperAutoCaller($this->r, new AnnotationsParser(function () { return array('method' => array()); }));
		$this->assertAttributeSame(array(), 'methods', $c);
	}

	public function test1()
	{
		$c = new MapperAutoCaller($this->r, new AnnotationsParser(function ($r) {
			if ($r->getName() !== 'TestsRepository') return array();
			return array(
				'method' => array(
					'mixed aaa(string $foo)',
					'mixed bbb()',
					'mixed ccc',
					'mixed ddd()',
					'mixed eee',
					'fff(string $foo)',
					'ggg()',
					'hhh',
				),
			);
		}));
		$this->assertAttributeSame(array(
			'aaa' => true,
			'bbb' => true,
			'ccc' => true,
			'ddd' => true,
			'eee' => true,
			'fff' => true,
			'ggg' => true,
			'hhh' => true,
		), 'methods', $c);
	}

	public function test2()
	{
		$c = new MapperAutoCaller($this->r, new AnnotationsParser(function ($r) {
			if ($r->getName() !== 'TestsRepository') return array();
			return array(
				'method' => array(
					'a1 b1 c1 d1',
					'asd|asd|FOO	bar',
					"\taaa\tbbb\t",
					"\t \t \t ccc\t \t \t ddd\t \t ",
					'fff() rrr()',
				),
			);
		}));
		$this->assertAttributeSame(array(
			'b1' => true,
			'bar' => true,
			'bbb' => true,
			'ddd' => true,
			'fff' => true,
		), 'methods', $c);
	}

	public function testEmpty()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method invalid format; '' given.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function () {
			return array('method' => array(''));
		}));
	}

	public function testTwice()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method cannot redeclare TestsRepository::aaa(); annotation already exists.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function () {
			return array('method' => array('aaa', 'aaa'));
		}));
	}

	public function testTwiceIgnoreCase()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method cannot redeclare TestsRepository::AAA(); annotation already exists.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function () {
			return array('method' => array('aaa', 'AAA'));
		}));
	}

	public function testRedeclareRealMethod1()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method cannot redeclare TestsRepository::getById(); method already exists.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function () {
			return array('method' => array('getById'));
		}));
	}

	public function testRedeclareRealMethod2()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "MapperAutoCaller_construct_Repository::@method cannot redeclare MapperAutoCaller_construct_Repository::aaa(); method already exists.");
		new MapperAutoCaller($this->m->{'MapperAutoCaller_construct_Repository'}, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'MapperAutoCaller_construct_Repository') return array('method' => array('aaa'));
			else if ($r->getName() === 'Orm\Repository') return array();
			throw new Exception;
		}));
	}

	public function testRedeclareRealMethod3()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method cannot redeclare MapperAutoCaller_construct_Repository::aaa(); method already exists.");
		$c = new MapperAutoCaller($this->m->{'MapperAutoCaller_construct_Repository'}, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'MapperAutoCaller_construct_Repository') return array();
			else if ($r->getName() === 'Orm\Repository') return array('method' => array('aaa'));
			throw new Exception;
		}));
	}

	public function testRedeclareRealMethodIgnoreCase()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "Orm\\Repository::@method cannot redeclare TestsRepository::GETBYID(); method already exists.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function () {
			return array('method' => array('GETBYID'));
		}));
	}

	public function testRedeclareProtectedMethod()
	{
		$c = new MapperAutoCaller($this->m->{'MapperAutoCaller_construct_Repository'}, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'MapperAutoCaller_construct_Repository') return array('method' => array('bbb'));
			else if ($r->getName() === 'Orm\Repository') return array();
			throw new Exception;
		}));
		$this->assertAttributeSame(array(
			'bbb' => true,
		), 'methods', $c);
	}

	public function testParent()
	{
		$c = new MapperAutoCaller($this->r, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'TestsRepository') return array('method' => array('aaa'));
			else if ($r->getName() === 'Orm\Repository') return array('method' => array('bbb'));
			throw new Exception;
		}));
		$this->assertAttributeSame(array(
			'bbb' => true,
			'aaa' => true,
		), 'methods', $c);
	}

	public function testParentTwice()
	{
		$this->setExpectedException('Orm\MapperAutoCallerException', "TestsRepository::@method cannot redeclare TestsRepository::aaa(); annotation already exists.");
		new MapperAutoCaller($this->r, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'TestsRepository') return array('method' => array('aaa'));
			else if ($r->getName() === 'Orm\Repository') return array('method' => array('aaa'));
			throw new Exception;
		}));
	}

	public function testIgnoreCase()
	{
		$c = new MapperAutoCaller($this->m->{'MapperAutoCaller_construct_Repository'}, new AnnotationsParser(function ($r) {
			if ($r->getName() === 'MapperAutoCaller_construct_Repository') return array('method' => array('BBB'));
			else if ($r->getName() === 'Orm\Repository') return array();
			throw new Exception;
		}));
		$this->assertAttributeSame(array('BBB' => true, 'bbb' => true), 'methods', $c);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperAutoCaller', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
