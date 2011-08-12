<?php

abstract class InterfaceTestCase extends TestCase
{
	protected $interface;
	protected $methodCounts = 0;

	protected $reflection;

	protected function setUp()
	{
		$this->reflection = new ReflectionClass($this->interface);
	}

	final public function test()
	{
		$this->assertTrue($this->reflection->isInterface());
		$this->assertSame($this->methodCounts, count($this->reflection->getMethods()));
	}

	public function assertMethod($method, $params)
	{
		$r = $this->reflection->getMethod($method);
		$this->assertSame($method, $r->getName());
		$this->assertSame($params, $this->formatMethodParams($r));
	}

	private function formatMethodParams(ReflectionMethod $m)
	{
		$tmp = array();
		foreach ($m->getParameters() as $p)
		{
			$tmp[] =
				($p->isArray() ? 'array ' : ($p->getClass() ? $p->getClass()->getName() . ' ' : '')) .
				($p->isPassedByReference() ? '& ' : '') .
				'$' . $p->getName() .
				($p->isDefaultValueAvailable() ? ' = ' . $p->getDefaultValue() : '')
			;
		}
		return implode(', ', $tmp);
	}
}
