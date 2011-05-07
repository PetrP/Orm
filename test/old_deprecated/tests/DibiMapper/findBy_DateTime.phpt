<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;


class Test extends Entity
{

}

class TestRepository extends Repository
{

}

dt($model->test->mapper->findByXyz(new DateTime('2010-11-03 15:10:01'))->__toString());


__halt_compiler();
------EXPECT------
"
SELECT *
	FROM `test`
	WHERE (`xyz` = '2010-11-03 15:10:01')
"
