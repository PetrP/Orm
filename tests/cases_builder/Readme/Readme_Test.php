<?php

use Nette\Environment;
use Orm\Builder\Readme;

/**
 * @covers Orm\Builder\Readme
 */
class Readme_Test extends TestCase
{
	private $from;
	private $to;
	private $info;
	protected function setUp()
	{
		$this->from = tempnam(__DIR__ . '/../../tmp', __CLASS__);
		$this->to = tempnam(__DIR__ . '/../../tmp', __CLASS__);
		$this->info = new TestVersionInfo('v0.3.1', '2011-11-11');
	}

	private function f($c)
	{
		file_put_contents($this->from, $c);
	}

	private function a($c)
	{
		$r = new Readme($this->from, $this->to, $this->info);
		$this->assertSame($c, file_get_contents($this->to));
	}

	public function testHead()
	{
		$this->f("Orm\n===\n\n");
		$this->a("\nOrm v0.3.1 released on 2011-11-11\n=================================\n\n");
	}

	public function testPre1()
	{
		$this->f("```php\necho 'hello';\n```");
		$this->a("echo 'hello';\n");
	}

	public function testPre2()
	{
		$this->f("```php\necho 'hello';\n```\n");
		$this->a("echo 'hello';\n");
	}

	public function testPre3()
	{
		$this->f("\n```php\necho 'hello';\n```\n");
		$this->a("\necho 'hello';\n");
	}

	public function testPre4()
	{
		$this->f("\n```php\necho 'hello';\n```");
		$this->a("\necho 'hello';\n");
	}

	public function testLink()
	{
		$this->f("[Foo bar](http://foo.bar)");
		$this->a("Foo bar (http://foo.bar)");
	}
}
