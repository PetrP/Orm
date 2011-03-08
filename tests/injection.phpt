<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property NotExistsInjection $test {injection}
 */
class NotExists extends Entity {}
try { new NotExists; } catch (Exception $e) {dt($e);}

/**
 * @property NotImplementsInjection $test {injection}
 */
class NotImplements extends Entity {}
class NotImplementsInjection {}
try { new NotImplements; } catch (Exception $e) {dt($e);}

/**
 * @property TestConstructInjection $test {injection}
 */
class TestConstruct extends Entity {}
class TestConstructInjection extends Injection
{
	function __construct($value, IEntity $entity = NULL){}
}
$t = new TestConstruct;
try { dt($t->test); } catch (Exception $e) {dt($e);}


/**
 * @property TestInjection $inj {injection}
 */
class Test extends Entity {}
class TestInjection extends Injection
{

}

$t = new Test;
try { dt($t->inj); } catch (Exception $e) {dt($e);}
try { $t->inj = 'xxx'; dt($t->inj); } catch (Exception $e) {dt($e);}

__halt_compiler();
------EXPECT------
Exception InvalidArgumentException: Not exists

Exception InvalidArgumentException: NotImplementsInjection not instantiable

Exception InvalidStateException: TestConstructInjection has required parameters in constructor, use custom factory

object(TestInjection) (1) {
	"value" protected => NULL
}

object(TestInjection) (1) {
	"value" protected => string(3) "xxx"
}
