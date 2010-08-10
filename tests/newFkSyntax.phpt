<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property Foo $foo {ManyToOne Foos}
 * @property Bar $bar {OneToOne Foos}
 * @property Bar $old
 * @fk $old Foos
 * @property Foo $x1 {m:1 Foos}
 * @property Bar $x2 {1:1 Foos}
 */
class Foo extends Entity
{

}

class FoosRepository extends Repository
{

}

dt(Foo::getFk('Foo'));

__halt_compiler();
------EXPECT------
array(%i%) {
	"foo" => string(4) "Foos"
	"bar" => string(4) "Foos"
	"old" => string(4) "Foos"
	"x1" => string(4) "Foos"
	"x2" => string(4) "Foos"
}
