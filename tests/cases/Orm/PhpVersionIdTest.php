<?php

class PhpVersionIdTest extends TestCase
{
	public function test()
	{
		$this->assertTrue(defined('PHP_VERSION_ID'));
		$tmp = explode('.', PHP_VERSION);
		$this->assertSame(($tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2]), PHP_VERSION_ID);
	}
}
