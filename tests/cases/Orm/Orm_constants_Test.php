<?php

use Orm\Orm;

/**
 * @covers Orm\Orm
 */
class Orm_constants_Test extends TestCase
{

	public function test()
	{
		$this->assertInternalType('string', Orm::VERSION);
		$this->assertTrue(is_int(Orm::VERSION_ID) OR is_float(Orm::VERSION_ID));
		$this->assertInternalType('string', Orm::REVISION);
		$this->assertContains(' released on ', Orm::REVISION);
		$this->assertInternalType('string', Orm::PACKAGE);
	}

	public function testBuildOnly()
	{
		if (
			Orm::VERSION_ID === 0 AND
			strpos(Orm::VERSION, '<build:') === 0 AND
			strpos(Orm::REVISION, '<build:') === 0 AND
			strpos(Orm::PACKAGE, '<build:') === 0
		)
		{
			$this->markTestSkipped('builded version only');
		}

		if (Orm::VERSION_ID === -1)
		{
			$this->assertSame('0.0.0-dev0', Orm::VERSION);
		}
		else if (Orm::VERSION_ID > 0)
		{
			$re = '(^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{1,2})$)';
			if (is_float(Orm::VERSION_ID))
			{
				$re = substr($re, 0, -2) . '-(dev|alfa|beta|RC)([0-9]{1,2})$)';
			}
			$this->assertRegExp($re, Orm::VERSION);
			preg_match($re, Orm::VERSION, $match);
			list(, $gen, $major, $minor, $stage, $stageNumber) = $match + array(4 => NULL, 5 => NULL);
			$id = $gen * 10000 + $major * 100 + $minor;
			if ($stage !== NULL)
			{
				$stages = array('dev' => 5, 'alfa' => 7, 'beta' => 8, 'RC' => 9);
				$id += ('0.' . str_repeat($stages[$stage], $stageNumber)) - 1;
			}
			$this->assertSame($id, Orm::VERSION_ID);
		}
		else
		{
			$this->fail();
		}
		$this->assertRegExp('(^[0-9a-f]{7} released on 20[0-9]{2}-(?:0[1-9]|1[012])-(?:0[1-9]|[12][0-9]|3[01])$)', Orm::REVISION);
		$this->assertTrue(in_array(Orm::PACKAGE, array('5.3', '5.2'), true));
		$this->assertSame(strpos('Orm\Orm', '\\') ? '5.3' : '5.2', Orm::PACKAGE);
	}

}
