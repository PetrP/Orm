<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsManyToMany
 */
class AnnotationMetaData_builtParamsManyToMany_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function testEmpty()
	{
		$this->assertSame(array(NULL, NULL, NULL), $this->p->builtParamsManyToMany(''));
		$this->assertSame(array(NULL, NULL, NULL), $this->p->builtParamsManyToMany('    '));
	}

	public function testOnlyOne()
	{
		$this->assertSame(array('repositoryName', NULL, NULL), $this->p->builtParamsManyToMany('repositoryName'));
		$this->assertSame(array('repositoryName', NULL, NULL), $this->p->builtParamsManyToMany('  repositoryName  '));
	}

	public function test()
	{
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName param'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName   param'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName   param  '));
	}

	public function testDolar()
	{
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName $param'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName   $param'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName   $param  '));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName $$param'));
	}

	public function testMore()
	{
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName param dalsi'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName   param   dalsi'));
		$this->assertSame(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName   param   dalsi  '));
	}

	public function testMap()
	{
		$this->assertSame(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName param mappedByThis'));
		$this->assertSame(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName   param   map'));
		$this->assertSame(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('  repositoryName   param   xxMaPxx  '));
		$this->assertSame(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName param mapped'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationMetaData', 'builtParamsManyToMany');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
