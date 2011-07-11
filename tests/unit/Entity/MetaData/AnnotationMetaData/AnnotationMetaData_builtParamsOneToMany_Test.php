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
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsOneToMany(''));
		$this->assertSame(array(NULL, NULL), $this->p->builtParamsOneToMany('    '));
	}

	public function testOnlyOne()
	{
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsOneToMany('repositoryName'));
		$this->assertSame(array('repositoryName', NULL), $this->p->builtParamsOneToMany('  repositoryName  '));
	}

	public function test()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName   param'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('  repositoryName   param  '));
	}

	public function testMore()
	{
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName param dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('repositoryName   param   dalsi'));
		$this->assertSame(array('repositoryName', 'param'), $this->p->builtParamsOneToMany('  repositoryName   param   dalsi  '));
	}

}
