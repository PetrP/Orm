<?php

use Orm\Events;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Events::__construct
 */
class Events_construct_Test extends TestCase
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Events', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function test()
	{
		$m = new RepositoryContainer;
		$events = new Events($m->tests);
		$this->assertInstanceOf('Orm\Events', $events);
		$this->assertAttributeSame($m->tests, 'repository', $events);
	}

	public function testConstancts()
	{
		$uses = array('a' => array(), 'i' => 0, 'c' => 0);

		$this->checkConstant(pow(2, 0), Events::HYDRATE_BEFORE, $uses);
		$this->checkConstant(pow(2, 1), Events::HYDRATE_AFTER, $uses);
		$this->checkConstant(pow(2, 2), Events::ATTACH, $uses);
		$this->checkConstant(pow(2, 3), Events::PERSIST_BEFORE, $uses);
		$this->checkConstant(pow(2, 4), Events::PERSIST_BEFORE_INSERT, $uses);
		$this->checkConstant(pow(2, 5), Events::PERSIST_BEFORE_UPDATE, $uses);
		$this->checkConstant(pow(2, 6), Events::PERSIST, $uses);
		$this->checkConstant(pow(2, 7), Events::PERSIST_AFTER_INSERT, $uses);
		$this->checkConstant(pow(2, 8), Events::PERSIST_AFTER_UPDATE, $uses);
		$this->checkConstant(pow(2, 9), Events::PERSIST_AFTER, $uses);
		$this->checkConstant(pow(2, 10), Events::REMOVE_BEFORE, $uses);
		$this->checkConstant(pow(2, 11), Events::REMOVE_AFTER, $uses);
		$this->checkConstant(pow(2, 12), Events::FLUSH_BEFORE, $uses);
		$this->checkConstant(pow(2, 13), Events::FLUSH_AFTER, $uses);
		$this->checkConstant(pow(2, 14), Events::CLEAN_BEFORE, $uses);
		$this->checkConstant(pow(2, 15), Events::CLEAN_AFTER, $uses);
		$this->checkConstant(pow(2, 16), Events::SERIALIZE_BEFORE, $uses);
		$this->checkConstant(pow(2, 17), Events::SERIALIZE_AFTER, $uses);
		$this->checkConstant(pow(2, 18), Events::SERIALIZE_CONVENTIONAL, $uses);

		$r = new ReflectionClass('Orm\Events');
		$c = $r->getConstants();
		$this->assertSame($uses['c'], count($c));
		foreach ($c as $key => $value)
		{
			if (!isset($uses['a'][$value]))
			{
				$this->fail('a');
			}
		}
	}

	private function checkConstant($int, $const, array & $uses)
	{
		$this->assertSame($int, $const);
		if (isset($uses['a'][$const]))
		{
			$this->fail('a');
		}
		if ($uses['i'] & $const)
		{
			$this->fail('i');
		}
		$uses['a'][$const] = $const;
		$uses['i'] |= $const;
		$uses['c']++;
	}
}
