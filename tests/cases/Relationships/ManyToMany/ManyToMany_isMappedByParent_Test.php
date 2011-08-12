<?php

use Orm\ManyToMany;

/**
 * @covers Orm\ManyToMany::isMappedByParent
 */
class ManyToMany_isMappedByParent_Test extends ManyToMany_Test
{

	public function testIs()
	{
		$this->m2m = new ManyToMany($this->e, $this->r->getRepositoryName(), 'param', 'param', true);
		$this->assertTrue($this->m2m->isMappedByParent());
	}

	public function testNot()
	{
		$this->m2m = new ManyToMany($this->e, $this->r->getRepositoryName(), 'param', 'param', false);
		$this->assertFalse($this->m2m->isMappedByParent());
	}

}
