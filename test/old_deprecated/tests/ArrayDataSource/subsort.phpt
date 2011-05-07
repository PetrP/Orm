<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;
/**
 * @property-read string $rand
 * @property Test|NULL $sub
 */
class Test extends Entity
{
	public function __construct(Test $sub = NULL)
	{
		parent::__construct();
		$this->sub = $sub;
	}

	public function getRand()
	{
		return lcg_value();
	}
}

$ds = new ArrayDataSource(array(
	new Test(new Test()),
	new Test(new Test()),
	new Test(),
	new Test(),
));

foreach ($ds->orderBy('sub->rand')->orderBy('rand') as $e)
{
	dt($e->rand . ' ' . (isset($e->sub) ? $e->sub->rand : NULL));
}

__halt_compiler();
------EXPECT------
"0.%i% "

"0.%i% "

"0.%i% 0.%i%"

"0.%i% 0.%i%"
