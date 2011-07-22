<?php

/**
 * @covers Orm\ValueEntityFragment::__get
 * @covers Orm\ValueEntityFragment::__call
 * @covers Orm\ValueEntityFragment::getValue
 */
class ValueEntityFragment_getter_Test extends ValueEntityFragment_settergetter_Base
{
	protected function a(ValueEntityFragment_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1, $bugValue = NULL)
	{
		$string = md5(lcg_value());
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
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
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
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 0);
		$this->a($e, $key, 2, 1, NULL); // bug neprecte se

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1, NULL); // bug neprecte se
		$this->a($e, $key, 2, 1, NULL); // bug neprecte se
		$this->a($e, $key, 3, 1, NULL); // bug neprecte se
		$this->a($e, $key, 5, 2, NULL); // bug zavola se 2krat (ma byt 4) // bug neprecte se

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1, NULL); // bug neprecte se
		$this->a($e, $key, 3, 2, NULL); // bug zavola se 2krat (ma byt 2) // bug neprecte se

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 2, 2, NULL); // bug zavola se 2krat // bug neprecte se

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 3);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 0, 4); // bug pri tomhle volani se nezavola getter
	}

	public function testException()
	{
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$key = 'exception';

		$e->throw = true;
		$ee = NULL;
		try {
			$e->$key;
			throw new Exception;
		} catch (ValueEntityFragment_getter_Test_Exception $ee) {}

		$e->throw = false;
		$this->php533bugAddExcepted('g', 'exception', 'getException');
		$this->php533bugAddExcepted('s', 'exception', 'setException');
		$this->a($e, $key, 2);
	}

	public function testNoParent()
	{
		$key = 'noParentGet';
		$method = "get$key";
		$uckey = ucfirst($key);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(2, $e->{"get{$uckey}Count"});

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'c';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"get{$uckey}Count"});

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$method());
		$this->assertSame(1, $e->{"get{$uckey}Count"});

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
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
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther', 'setCallOther');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 1, 1);
		$this->assertSame(1, $e->getNewCount);
		$this->assertSame(1, $e->getOldCount);
		$this->assertSame(1, $e->getNoParentGetCount);
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->new);
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->old);
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther', 'setCallOther');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 5, 2);
		$this->assertSame(6, $e->getNewCount);
		$this->assertSame(6, $e->getOldCount);
		$this->assertSame(6, $e->getNoParentGetCount);
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->new);
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->old);
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$key = 'callOther2';

		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther2', 'setCallOther2');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 1, 1);
		$this->assertSame(11, $e->getNewCount);
		$this->assertSame(11, $e->getOldCount);
		$this->assertSame(11, $e->getNoParentGetCount);
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->new);
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->old);
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);

		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther2', 'setCallOther2');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 5, 2);
		$this->assertSame(16, $e->getNewCount);
		$this->assertSame(16, $e->getOldCount);
		$this->assertSame(16, $e->getNoParentGetCount);
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->new);
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->old);
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->assertSame($e->$key, $e->withoutMethod);
		$this->assertSame(NULL, $e->noParentGet);
		$this->assertSame(NULL, $e->noParentSet);
	}

}
