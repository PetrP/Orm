<?php

require dirname(__FILE__) . '/base.php';

interface IFoo {}
interface IBar {}

class Foo implements IFoo
{

}

class Bar extends Foo implements IBar
{

}

class Maa extends Bar
{

}


class Test
{

	function is_subclass_of($a, $b)
	{
		return is_subclass_of($a, $b);
	}

	function class_implements($a, $b)
	{
		$implements = class_implements($a);
		return isset($implements[$b]);
	}

	function class_parents($a, $b)
	{
		$parent = class_parents($a);
		return isset($parent[$b]);
	}

	function Reflection_implementsInterface($a, $b)
	{
		$reflection = new ReflectionClass($a);
		return $reflection->implementsInterface($b);
	}

	function Reflection_isSubclassOf($a, $b)
	{
		$reflection = new ReflectionClass($a);
		return $reflection->isSubclassOf($b);
	}

}

$times = 1000;

$test = array(
	'Bar' => 'IBar',
	'Bar' => 'IFoo',
	'Foo' => 'IFoo',
);

$timers = array();
$t = new Test;
foreach (array(
	'class_implements',
	'Reflection_implementsInterface',
	'Reflection_isSubclassOf',
) as $method)
{
	Debug::timer();
	for ($i=$times;$i>0;$i--)
	{
		foreach ($test as $a => $b)
		{
			Assert::true($t->{$method}($a, $b));
		}
	}
	$timers[$method] = Debug::timer();
}
asort($timers);
dt($timers);
$timers = array();

$test = array(
	'Bar' => 'Foo',
	'Maa' => 'Bar',
	'Maa' => 'Foo',
);

$timers = array();
$t = new Test;
foreach (array(
	'is_subclass_of',
	'class_parents',
	'Reflection_isSubclassOf',
) as $method)
{
	Debug::timer();
	for ($i=$times;$i>0;$i--)
	{
		foreach ($test as $a => $b)
		{
			if (!$t->{$method}($a, $b))
			{
				dt(false, $method);
			}
		}
	}
	$timers[$method] = Debug::timer();
}
asort($timers);
dt($timers);


__halt_compiler();
------EXPECT------
array(
	"class_implements" => %f%
	"Reflection_implementsInterface" => %f%
	"Reflection_isSubclassOf" => %f%
)

array(
	"is_subclass_of" => %f%
	"class_parents" => %f%
	"Reflection_isSubclassOf" => %f%
)
