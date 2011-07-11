<?php

/**
 * @covers Orm\AnnotationMetaData::builtParamsOneToMany
 */
class AnnotationMetaData_builtParamsOneToMany_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = MockAnnotationMetaData::mockConstruct('MetaData_Test_Entity');
	}

	public function testEmpty()
	{
		$this->assertEquals(array(NULL, NULL), $this->p->builtParamsOneToMany(''));
		$this->assertEquals(array(NULL, NULL), $this->p->builtParamsOneToMany('    '));
	}

	public function testOnlyOne()
	{
		$this->assertEquals(array('repositoryName', NULL), $this->p->builtParamsOneToMany('repositoryName'));
		$this->assertEquals(array('repositoryName', NULL), $this->p->builtParamsOneToMany('  repositoryName  '));
	}

	public function test()
	{
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName param'));
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName   param'));
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('  repositoryName   param  '));
	}

	public function testMore()
	{
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName param dalsi'));
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName   param   dalsi'));
		$this->assertEquals(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('  repositoryName   param   dalsi  '));
	}

}
