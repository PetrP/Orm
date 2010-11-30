<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;
/**
 * @property string $x
 * @property string $y
 */
class Test extends Entity
{
	function __construct($x, $y)
	{
		parent::__construct();
		$this->x = $x;
		$this->y = $y;
	}
}

$ds = new ArrayDataSource(array(
	new Test(1,2), new Test(2,2), new Test(1,1),
));

dt($ds->findByXAndY(1,2)->count());
try { dt($ds->findByXAndY(1)->count()); } catch (Exception $e) {dt($e);}
try { dt($ds->findByXAndY(1,2,3)->count()); } catch (Exception $e) {dt($e);}

__halt_compiler();
------EXPECT------
1

Exception InvalidArgumentException: There is no value for 'y' in 'findByXAndY'.

Exception InvalidArgumentException: There is extra value in 'findByXAndY'.
