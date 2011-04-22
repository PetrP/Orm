<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::__get
 * @covers _EntityValue::__call
 */
class EntityValue_setter_Test extends EntityValue_settergetter_Base
{
	protected function a(EntityValue_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1)
	{
		$string = String::random();
		if ($callmode === 0)
		{
			$string = NULL;
			// default value
		}
		else if ($callmode === 1)
		{
			$e->$key = $string;
		}
		else if ($callmode === 2)
		{
			$e->{"set$key"}($string);
		}
		else if ($callmode === 3)
		{
			$e->__set($key, $string);
		}
		else if ($callmode === 4)
		{
			$e->__call("set$key", array($string));
			// todo pri tomhle volani se nezavola setter
		}
		$e->$key;
		$uckey = ucfirst($key);
		if ($count !== NULL) $this->assertSame($count, $e->{"set{$uckey}Count"});
		$this->assertSame($string, $e->$key);
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
		$key = 'newByPropertySet';
		$testCount = true;
		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 0);
		$this->a($e, $key, 2, 1);

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->a($e, $key, 1, 1); // bug nezavola se
		$this->a($e, $key, 1, 1); // bug nezavola se
		$this->a($e, $key, 2, 2);

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->a($e, $key, 2, 2);

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 2, 2); // bug zavola se 2krat

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 3);

		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 0, 4); // bug pri tomhle volani se nezavola setter
	}

	public function testException()
	{
		$e = new EntityValue_gettersetter_Test_Entity;
		$key = 'exception';

		$e->throw = true;
		$ee = NULL;
		try {
			$e->$key = 3;
		} catch (EntityValue_setter_Test_Exception $ee) {}
		$this->assertException($ee, 'EntityValue_setter_Test_Exception', '');
		$e->throw = false;
		$this->a($e, $key, 2);
	}

	public function testNoParent()
	{
		$key = 'noParentSet';
		$method = "set$key";
		$uckey = ucfirst($key);

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"set{$uckey}Count"}); // 3x protoze default

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key); // zavola se default
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'c';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(4, $e->{"set{$uckey}Count"}); // 4x protoze default

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$method('a');
		$e->$method('b');
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"set{$uckey}Count"}); // 3x protoze default

		$e = new EntityValue_gettersetter_Test_Entity;
		$e->$method('a');
		$this->assertSame(NULL, $e->$key);
		$e->$method('b');
		$this->assertSame(NULL, $e->$key);
		$e->$method('c');
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(4, $e->{"set{$uckey}Count"}); // 4x protoze default

	}

	public function testCallOther()
	{
		$key = 'callOther';
		$e = new EntityValue_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->assertSame(1, $e->setNewCount);
		$this->assertSame(1, $e->setOldCount);
		$this->assertSame(2, $e->setNoParentSetCount); // 2x protoze default
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->a($e, $key, 2, 2);
		$this->assertSame(2, $e->setNewCount);
		$this->assertSame(2, $e->setOldCount);
		$this->assertSame(3, $e->setNoParentSetCount); // 3x protoze default
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$key = 'callOther2';

		$this->a($e, $key, 1, 1);
		$this->assertSame(3, $e->setNewCount);
		$this->assertSame(3, $e->setOldCount);
		$this->assertSame(4, $e->setNoParentSetCount); // 4x protoze default
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->a($e, $key, 2, 2);
		$this->assertSame(4, $e->setNewCount);
		$this->assertSame(4, $e->setOldCount);
		$this->assertSame(5, $e->setNoParentSetCount); // 5x protoze default
		$this->assertSame($e->$key, $e->new);
		$this->assertSame($e->$key, $e->old);
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);
	}

}
