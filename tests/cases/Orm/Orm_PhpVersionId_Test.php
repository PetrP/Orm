<?php

class Orm_PhpVersionId_Test extends TestCase
{

	public function test()
	{
		$this->assertTrue(defined('PHP_VERSION_ID'));
		$this->assertSame(PHP_VERSION, phpversion());
		if (PHP_VERSION === '5.4.6' AND PHP_VERSION_ID === 50405)
		{
			// V 5.4.6 zapomeli zmenit PHP_VERSION_ID.
			// https://github.com/php/php-src/commit/3548b2e2c5bbe361c3c95a4df563e81f33da4b7e#diff-6b877bd60916a7ba8fd1c6028b1b28aaR8
			$this->assertSame('5.4.6', PHP_VERSION);
			$this->assertSame('5.4.6', implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION)));
			$this->assertSame(50405, PHP_VERSION_ID);
			$this->markTestSkipped('php 5.4.6 bug');
		}
		$tmp = explode('.', PHP_VERSION);
		$this->assertSame(($tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2]), PHP_VERSION_ID);
		if (PHP_VERSION_ID >= 50207)
		{
			if (PHP_VERSION_ID === 50313)
			{
				// V 5.3.13 je spatne PHP_RELEASE_VERSION.
				// https://github.com/php/php-src/commit/e9354b53665e2d313f07d48ce3d227cc61a068dc#diff-6b877bd60916a7ba8fd1c6028b1b28aaR5
				$this->assertSame('5.3.12', implode('.', array(PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION)));
				$this->markTestSkipped('php 5.3.13 bug');
			}
			$this->assertSame((PHP_MAJOR_VERSION * 10000 + PHP_MINOR_VERSION * 100 + PHP_RELEASE_VERSION), PHP_VERSION_ID);
		}
	}

}
