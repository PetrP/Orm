<?php

use Nette\NotImplementedException;

class TestGit extends Orm\Builder\Git
{
	private $expected = array();

	public function getPath()
	{
		throw new NotImplementedException;
	}

	public function __construct()
	{

	}

	public function command($cmd, array $env = NULL, $throwError = true)
	{
		if ($env !== NULL OR $throwError !== true) throw new NotImplementedException;
		list($command, $output) = array_shift($this->expected);
		PHPUnit_Framework_Assert::assertSame($command, $cmd);
		return $output;
	}

	public function getSha($input)
	{
		list($command, $output) = array_shift($this->expected);
		PHPUnit_Framework_Assert::assertSame($command, $input);
		return $output;
	}

	public function set($command, $output)
	{
		$this->expected[] = array($command, $output);
	}

	public function __destruct()
	{
		PHPUnit_Framework_Assert::assertSame(array(), $this->expected);
	}
}
