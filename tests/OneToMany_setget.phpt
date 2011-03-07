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
 * @property Foo|NULL $foo {m:1 Foos}
 */
class Bar extends Entity
{
	function __construct($foo = NULL)
	{
		parent::__construct();
		$this->foo = $foo;
	}
}
class BarsRepository extends Repository
{
}
class FoosRepository extends Repository
{
}
abstract class BaseMapper extends ArrayMapper
{
	public $data = array();
	
	protected function loadData()
	{
		return $this->data;
	}
	protected function saveData(array $data)
	{
		$this->data = $data;
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
	foreach ($v as $e) $r[] = get_class($e) . '#' . (isset($e->id) ? $e->id : NULL);
	dt($r);
}

$foo = new Foo;
t($foo->bars);

$foo = new Foo;
$model->bars->persist(new Bar($foo));
$model->bars->persist(new Bar($foo));
t($foo->bars);

$foo = new Foo;
$model->bars->persist(new Bar($foo));
$model->bars->persist(new Bar($foo));
$foo->bars = array(new Bar);
t($foo->bars);

$foo = new Foo;
$model->bars->persist(new Bar($foo));
$model->bars->persist(new Bar($foo));
try{$foo->bars->add(array(new Bar));}catch(Exception $e){dt($e);}
t($foo->bars);
$foo->bars->remove(6);
t($foo->bars);
$model->bars->getById(1)->foo = NULL;
$foo->bars->add(1);
t($foo->bars);

__halt_compiler();
------EXPECT------
array()

array(
	"Bar#1"
	"Bar#2"
)

array(
	"Bar#"
)

array(
	"Bar#5"
	"Bar#6"
	"Bar#"
)

array(
	"Bar#5"
	"Bar#"
)

array(
	"Bar#5"
	"Bar#"
	"Bar#1"
)
