<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;
/**
 * @property int $t1
 * @property int $t2
 */
class Test extends Entity
{
	public function __construct($t1 = 5, $t2 = 10)
	{
		parent::__construct();
		$this->t1 = $t1;
		$this->t2 = $t2;
	}

}

class TestsRepository extends Repository
{

}

class TestsMapper extends FileMapper
{
	protected function getFilePath()
	{
		return dirname(__FILE__) . '/' . $this->getRepository()->getRepositoryName() . '.data';
	}
}

$r = $model->tests;

$r->persist(new Test);
$r->persist(new Test);
$r->persist(new Test);

dt($r->mapper->findByT1(5)->count());
dt($r->mapper->findByT1(4)->count());

dt($r->mapper->findByT2(10)->count());
dt($r->mapper->findByT2(9)->count());

dt($r->mapper->findByT1AndT2(4, 9)->count());
dt($r->mapper->findByT1AndT2(5, 9)->count());
dt($r->mapper->findByT1AndT2(4, 10)->count());

dt($r->mapper->findByT1AndT2(5, 10)->count());

$r->clean();

__halt_compiler();
------EXPECT------
3

0

3

0

0

0

0

3
