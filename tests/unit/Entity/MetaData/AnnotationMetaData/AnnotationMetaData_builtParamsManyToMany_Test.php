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
		$this->assertEquals(array(NULL, NULL, NULL), $this->p->builtParamsManyToMany(''));
		$this->assertEquals(array(NULL, NULL, NULL), $this->p->builtParamsManyToMany('    '));
	}

	public function testOnlyOne()
	{
		$this->assertEquals(array('repositoryName', NULL, NULL), $this->p->builtParamsManyToMany('repositoryName'));
		$this->assertEquals(array('repositoryName', NULL, NULL), $this->p->builtParamsManyToMany('  repositoryName  '));
	}

	public function test()
	{
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName param'));
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName   param'));
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName   param  '));
	}

	public function testMore()
	{
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName param dalsi'));
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('repositoryName   param   dalsi'));
		$this->assertEquals(array('repositoryName', 'param', NULL), $this->p->builtParamsManyToMany('  repositoryName   param   dalsi  '));
	}

	public function testMap()
	{
		$this->assertEquals(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName param mappedByThis'));
		$this->assertEquals(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName   param   map'));
		$this->assertEquals(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('  repositoryName   param   xxMaPxx  '));
		$this->assertEquals(array('repositoryName', 'param', true), $this->p->builtParamsManyToMany('repositoryName param mapped'));
	}

}
