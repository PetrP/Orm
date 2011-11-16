<?php

use Orm\Builder\Git;

/**
 * @covers Orm\Builder\Git
 */
class Git_Test extends TestCase
{
	private $git;

	protected function setUp()
	{
		$this->git = new Git(__DIR__ . '/../../..');
		Nette\Diagnostics\Debugger::$maxLen = 9999;
	}

	public function test()
	{
		$this->assertSame('2891122ef4c9c937189fcdfa08873b0caaecb63c', $this->git->getSha('v0.2.2'));
		$this->assertSame('', $this->git->command('diff-index HEAD -- Orm/'));
		$this->assertSame('', $this->git->command('ls-files --others --exclude-standard -- Orm/'));
		$this->assertSame("2011-09-21 00:31:53 +0200\n", $this->git->command('show -s 2891122ef4c --format="%ci"'));
		$this->assertSame(
			"df5cadbbd663ab07779a7fe760924acfc269fdf0 refs/tags/v0.1.0\n" .
			"8e2cd6859149bfbceeea20859e90ce752af8d395 refs/tags/v0.1.1\n" .
			"b7bf28bc3ff41bd5e02b03dd78cc3bb193018628 refs/tags/v0.1.2\n" .
			"7d626da8349c4ab9327db7a4a3cb0b7e7b4a831a refs/tags/v0.1.3\n",
			substr($this->git->command('show-ref --tags'), 0, 232));

		$this->assertSame("tag\n", $this->git->command('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42'));
		$this->assertSame("object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n", $this->git->command('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42'));
		$this->assertSame("object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n", $this->git->command('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb'));
	}
}
