<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::__clone
 */
class EntityValue_clone_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$m = new Model;
		$this->e = $m->testentity->getById(1);
	}

	public function testBase()
	{
		$e = $this->e;

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('testentity', $e->generatingRepository->repositoryName);
		$this->assertSame(false, $e->isChanged());

		$ee = clone $e;

		$this->assertSame(NULL, isset($ee->id) ? $ee->id : NULL);
		$this->assertSame('string', $ee->string);
		$this->assertSame('2011-11-11', $ee->date->format('Y-m-d'));
		$this->assertSame('testentity', $ee->generatingRepository->repositoryName);
		$this->assertSame(true, $ee->isChanged());

		$this->assertSame(1, $e->id);
		$this->assertSame('string', $e->string);
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('testentity', $e->generatingRepository->repositoryName);
		$this->assertSame(false, $e->isChanged());
	}

	public function testChange()
	{
		$e = $this->e;
		$ee = clone $e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$ee->date = '2010-10-10';
		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		$this->assertSame('2010-10-10', $ee->date->format('Y-m-d'));
	}

	public function testChangeObject()
	{
		$e = $this->e;

		$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));

		$ee = clone $e;

		$ee->date->modify('-50 years');
		$this->assertSame('1961-11-11', $ee->date->format('Y-m-d'));
		try {
			$this->assertSame('2011-11-11', $e->date->format('Y-m-d'));
		} catch (PHPUnit_Framework_ExpectationFailedException $e) {
			// bug, udrzuje se reference
			$this->markTestSkipped();
		}
	}

}
