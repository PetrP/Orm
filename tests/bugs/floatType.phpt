<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;
/**
 * @property float $float
 */
class Test extends Entity
{
	
}

$test = new Test;

dt($test->setFloat(1.5)->float);
dt($test->setFloat(2)->float);
dt($test->setFloat('3')->float);
dt($test->setFloat('3.5')->float);
try {dt($test->setFloat('4,5')->float);} catch (Exception $e) {dt($e);}
dt($test->setFloat('')->float);
try {dt($test->setFloat('4 505')->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat('4 505,526')->float);} catch (Exception $e) {dt($e);}


try {dt($test->setFloat(NULL)->float, 'NULL');} catch (Exception $e) {dt($e);}
try {dt($test->setFloat(TRUE)->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat(FALSE)->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat('text')->float);} catch (Exception $e) {dt($e);}

try {dt($test->setFloat('4,5,5')->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat('4.5.5')->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat('4.5.5,5')->float);} catch (Exception $e) {dt($e);}
try {dt($test->setFloat('4,5,5.5')->float);} catch (Exception $e) {dt($e);}


__halt_compiler();
------EXPECT------
1.5

2.0

3.0

3.5

4.5

0.0

4505.0

4505.526

NULL: 0.0

Exception UnexpectedValueException: Param Test::$float must be 'float', 'boolean' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'boolean' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'string' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'string' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'string' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'string' given

Exception UnexpectedValueException: Param Test::$float must be 'float', 'string' given
