<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsManyToOne
 */
class AnnotationMetaData_builtParamsManyToOne_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function testEmpty()
	{
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsManyToOne(''));
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsManyToOne('    '));
	}

	public function testOnlyOne()
	{
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsManyToOne('repositoryName'));
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsManyToOne('  repositoryName  '));
	}

	public function test()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName   param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('  repositoryName   param  '));
	}

	public function testDolar()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName $param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName   $param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('  repositoryName   $param  '));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('  repositoryName $$param'));
	}

	public function testMore()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName param dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('repositoryName   param   dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsManyToOne('  repositoryName   param   dalsi  '));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'builtParamsManyToOne');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
