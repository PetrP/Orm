<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsOneToOne
 */
class AnnotationMetaData_builtParamsOneToOne_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function testEmpty()
	{
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsOneToOne(''));
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsOneToOne('    '));
	}

	public function testOnlyOne()
	{
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsOneToOne('repositoryName'));
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsOneToOne('  repositoryName  '));
	}

	public function test()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName   param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('  repositoryName   param  '));
	}

	public function testDolar()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName $param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName   $param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('  repositoryName   $param  '));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('  repositoryName $$param'));
	}

	public function testMore()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName param dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('repositoryName   param   dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToOne('  repositoryName   param   dalsi  '));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'builtParamsOneToOne');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
