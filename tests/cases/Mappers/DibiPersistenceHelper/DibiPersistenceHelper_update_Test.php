<?php

/**
 * @covers Orm\DibiPersistenceHelper::update
 */
class DibiPersistenceHelper_update_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3, `aaa`='aaa' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$r = $this->h->call('update', array(array('id' => 3, 'aaa' => 'aaa'), 3));
		$this->assertSame(NULL, $r);
	}

	public function testPrimaryKey()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->d->addExpected('query', true, "UPDATE `table` SET `foo_bar`=3, `aaa`='aaa' WHERE `foo_bar` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$r = $this->h->call('update', array(array('foo_bar' => 3, 'aaa' => 'aaa'), 3));
		$this->assertSame(NULL, $r);
	}

	public function testEmpty()
	{
		$r = $this->h->call('update', array(array(), 3));
		$this->assertSame(NULL, $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'update');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
