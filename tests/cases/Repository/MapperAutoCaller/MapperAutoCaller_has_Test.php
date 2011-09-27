<?php

use Orm\RepositoryContainer;
use Orm\AnnotationsParser;
use Orm\MapperAutoCaller;

/**
 * @covers Orm\MapperAutoCaller::has
 */
class MapperAutoCaller_has_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->c = new MapperAutoCaller($m->tests, new AnnotationsParser(function ($r) {
			if ($r->getName() !== 'TestsRepository') return array();
			return array('method' => array('aaa', 'BBB'));
		}));
	}

	public function testFalse()
	{
		$this->assertSame(false, $this->c->has('abc'));
		$this->assertSame(false, $this->c->has('getById'));
	}

	public function testTrue()
	{
		$this->assertSame(true, $this->c->has('aaa'));
		$this->assertSame(true, $this->c->has('BBB'));
	}

	public function testIgnoreCase()
	{
		$this->assertSame(true, $this->c->has('AAA'));
		$this->assertSame(true, $this->c->has('bbb'));
	}

	public function testIgnoreCaseOptimalization()
	{
		$this->assertAttributeSame(array('aaa' => true, 'BBB' => true, 'bbb' => true), 'methods', $this->c);
		$this->assertSame(true, $this->c->has('AAA'));
		$this->assertAttributeSame(array('aaa' => true, 'BBB' => true, 'bbb' => true, 'AAA' => true), 'methods', $this->c);
		$this->assertSame(true, $this->c->has('AAA'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperAutoCaller', 'has');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
