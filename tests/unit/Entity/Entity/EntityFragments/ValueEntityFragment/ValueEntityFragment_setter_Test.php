<?php

/**
 * @covers Orm\ValueEntityFragment::__set
 * @covers Orm\ValueEntityFragment::__call
 * @covers Orm\ValueEntityFragment::setValue
 * @covers Orm\ValueEntityFragment::setValueHelper
 */
class ValueEntityFragment_setter_Test extends ValueEntityFragment_settergetter_Base
{
	protected function a(ValueEntityFragment_gettersetter_Test_Entity $e, $key, $count = NULL, $callmode = 1)
	{
		$string = md5(lcg_value());
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
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
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
		$key = 'newByPropertySet';
		$testCount = true;
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 0);
		$this->a($e, $key, 2, 1);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->a($e, $key, 1, 1); // bug nezavola se
		$this->a($e, $key, 1, 1); // bug nezavola se
		$this->a($e, $key, 2, 2);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 1);
		$this->a($e, $key, 2, 2);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 2, 2); // bug zavola se 2krat

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 1, 3);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->a($e, $key, 0, 4); // bug pri tomhle volani se nezavola setter
	}

	public function testException()
	{
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$key = 'exception';

		$e->throw = true;
		$ee = NULL;
		try {
			$e->$key = 3;
			throw new Exception;
		} catch (ValueEntityFragment_setter_Test_Exception $ee) {}

		$e->throw = false;
		$this->php533bugAddExcepted('g', 'exception', 'getException');
		$this->php533bugAddExcepted('g', 'exception', 'getException');
		$this->php533bugAddExcepted('s', 'exception', 'setException');
		$this->a($e, $key, 2);
	}

	public function testNoParent()
	{
		$key = 'noParentSet';
		$method = "set$key";
		$uckey = ucfirst($key);

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$key = 'a';
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"set{$uckey}Count"}); // 3x protoze default

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$key = 'a';
		$this->assertSame(NULL, $e->$key); // zavola se default
		$e->$key = 'b';
		$this->assertSame(NULL, $e->$key);
		$e->$key = 'c';
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(4, $e->{"set{$uckey}Count"}); // 4x protoze default

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$e->$method('a');
		$e->$method('b');
		$this->assertSame(NULL, $e->$key);
		$this->assertSame(3, $e->{"set{$uckey}Count"}); // 3x protoze default

		$e = new ValueEntityFragment_gettersetter_Test_Entity;
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
		$e = new ValueEntityFragment_gettersetter_Test_Entity;
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther', 'setCallOther');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 1, 1);
		$this->assertSame(1, $e->setNewCount);
		$this->assertSame(1, $e->setOldCount);
		$this->assertSame(2, $e->setNoParentSetCount); // 2x protoze default
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
		$this->php533bugAddExcepted('g', 'callOther', 'getCallOther');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther', 'setCallOther');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 2, 2);
		$this->assertSame(2, $e->setNewCount);
		$this->assertSame(2, $e->setOldCount);
		$this->assertSame(3, $e->setNoParentSetCount); // 3x protoze default
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
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther2', 'setCallOther2');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 1, 1);
		$this->assertSame(3, $e->setNewCount);
		$this->assertSame(3, $e->setOldCount);
		$this->assertSame(4, $e->setNoParentSetCount); // 4x protoze default
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
		$this->php533bugAddExcepted('g', 'callOther2', 'getCallOther2');
		$this->php533bugAddExcepted('g', 'new', 'getNew');
		$this->php533bugAddExcepted('s', 'callOther2', 'setCallOther2');
		$this->php533bugAddExcepted('s', 'new', 'setNew');
		$this->a($e, $key, 2, 2);
		$this->assertSame(4, $e->setNewCount);
		$this->assertSame(4, $e->setOldCount);
		$this->assertSame(5, $e->setNoParentSetCount); // 5x protoze default
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
