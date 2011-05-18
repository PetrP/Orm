<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityValue::__get
 * @covers Orm\_EntityValue::__call
 * @covers Orm\_EntityValue::getValue
 */
class EntityValue_getter_Test extends EntityValue_settergetter_Base
{
	protected function a(EntityValue_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1, $bugValue = NULL)
	{
		$string = String::random();
		if ($callmode === 0)
		{
			$string = NULL;
			$value = $e->$key;
			// default value
		}
		else
		{
			$e->$key = $string;
			if ($callmode === 1)
			{
				$value = $e->$key;
			}
			else if ($callmode === 2)
			{
				$value = $e->{"get$key"}();
			}
			else if ($callmode === 3)
			{
				$value = $e->__get($key);
			}
			else if ($callmode === 4)
			{
				$value = $e->__call("get$key", array());
				// todo pri tomhle volani se nezavola getter
			}
		}
		// todo is*()
		$uckey = ucfirst($key);
		if ($count !== NULL) $this->assertSame($count, $e->{"get{$uckey}Count"});
		if (func_num_args() >= 5) $string = $bugValue;
		$this->assertSame($string, $value);
	}

	public function testOld()
	{
		$this->x('old');
	}

	public function testNew()
	{
		$this->x('new');
	}

	public function testWithoutMethod()
	{
		$this->x('withoutMethod', false);
	}

	public function testNewByProperty()
	{
		// zadavani pomoci property neni podporovano
		$key = 'newByPropertyGet';
		$testCount = true;
		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 0);
		$this->a($e, $key, 2, 1, NULL); // bug neprecte se

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1, NULL); // bug neprecte se
		$this->a($e, $key, 2, 1, NULL); // bug neprecte se
		$this->a($e, $key, 3, 1, NULL); // bug neprecte se
		$this->a($e, $key, 5, 2, NULL); // bug zavola se 2krat (ma byt 4) // bug neprecte se

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1, NULL); // bug neprecte se
		$this->a($e, $key, 3, 2, NULL); // bug zavola se 2krat (ma byt 2) // bug neprecte se

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 2, 2, NULL); // bug zavola se 2krat // bug neprecte se

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 3);

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 0, 4); // bug pri tomhle volani se nezavola getter
	}

	public function testException()
	{
		$e = new EntityValue_gettersetter_Test_Entity;
		$key = 'exception';

		$e->throw = true;
		$ee = NULL;
		try {
			$e->$key;
		} catch (EntityValue_getter_Test_Exception $ee) {}
		$this->assertException($ee, 'EntityValue_getter_Test_Exception', '');
		$e->throw = false;
		$this->a($e, $key, 2);
	}

	public function testNoParent()
	{
		$key = 'noParentGet';
		$method = "get$key";
		$uckey = ucfirst($key);

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(2, $e->{"get{$uckey}Count"});

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'c';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"get{$uckey}Count"});

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$method());
		$this->assertSame(1, $e->{"get{$uckey}Count"});

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$method());
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$method());
		$e->$key = 'c';
		$this->assertSame(NULL, $e->$method());
		$this->assertSame(3, $e->{"get{$uckey}Count"});

	}

	public function testCallOther()
	{
		$key = 'callOther';
		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->assertSame(1, $e->getNewCount);
		$this->assertSame(1, $e->getOldCount);
		$this->assertSame(1, $e->getNoParentGetCount);
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->a($e, $key, 5, 2);
		$this->assertSame(6, $e->getNewCount);
		$this->assertSame(6, $e->getOldCount);
		$this->assertSame(6, $e->getNoParentGetCount);
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$key = 'callOther2';

		$this->a($e, $key, 1, 1);
		$this->assertSame(11, $e->getNewCount);
		$this->assertSame(11, $e->getOldCount);
		$this->assertSame(11, $e->getNoParentGetCount);
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->a($e, $key, 5, 2);
		$this->assertSame(16, $e->getNewCount);
		$this->assertSame(16, $e->getOldCount);
		$this->assertSame(16, $e->getNoParentGetCount);
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);
	}

}
