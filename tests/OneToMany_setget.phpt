<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;
/**
 * @property FooToBars $bars {1:m}
 */
class Foo extends Entity
{

}

/**
 * @property Foo|NULL $foo {m:1}
 */
class Bar extends Entity
{

}
class BarsRepository extends Repository
{
	static $mock;
	
	public function findByFoo()
	{
		foreach (self::$mock as $bar)
		{
			$this->persist($bar);
		}
		return new ArrayDataSource(self::$mock);
	}
}
class FoosRepository extends Repository
{

}
abstract class BaseMapper extends ArrayMapper
{
	protected function loadData()
	{
		return array();
	}
	protected function saveData(array $data)
	{

	}
}
class BarsMapper extends BaseMapper{}
class FoosMapper extends BaseMapper{}

class FooToBars extends OneToMany
{
	
}

function t($v)
{
	$r = array();
	foreach ($v as $e) $r[] = get_class($e);
	dt($r);
}

$foo = new Foo;
BarsRepository::$mock = array();
t($foo->bars->get()->fetchAll());

$foo = new Foo;
BarsRepository::$mock = array(new Bar, new Bar);
t($foo->bars->get()->fetchAll());

$foo = new Foo;
BarsRepository::$mock = array(new Bar, new Bar);
$foo->bars = array(new Bar);
t($foo->bars->get()->fetchAll());

$foo = new Foo;
BarsRepository::$mock = array(new Bar, new Bar);
try{$foo->bars->add(array(new Bar));}catch(Exception $e){dt($e);}
t($foo->bars->get()->fetchAll());

__halt_compiler();
------EXPECT------
array()

array(
	"Bar"
	"Bar"
)

array(
	"Bar"
)

array(
	"Bar"
	"Bar"
	"Bar"
)