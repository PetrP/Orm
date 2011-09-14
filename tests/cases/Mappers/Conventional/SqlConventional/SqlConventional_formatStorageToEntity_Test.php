<?php

/**
 * @covers Orm\SqlConventional::formatStorageToEntity
 * @covers Orm\SqlConventional::entityFormat
 */
class SqlConventional_formatStorageToEntity_Test extends SqlConventional_formatEntityToStorage_Test
{

	protected function t($entry, $expected)
	{
		$tmp = (string) key($this->c->formatStorageToEntity(array($entry => NULL)));
		$this->assertSame($expected, $tmp);
		return $tmp;
	}

	protected function tt(array $entry, array $expected)
	{
		$this->assertSame($expected, $this->c->formatStorageToEntity($entry));
	}

	public function test()
	{
		$this->t('id', 'id');
		$this->t('same_thing', 'sameThing');
		$this->t('sameThing', 'samething');
		$this->t('same1_thing', 'same1Thing');
		$this->t('a_b_c', 'aBC');
		$this->t('123', '123');
		$this->t('1b_c', '1bC');
		$this->t('same_thing_same_thing', 'sameThingSameThing');

		$this->t('_a_b_c', 'ABC');
		$this->t('__a_b_c', '_ABC');
	}

	public function testSpecialChar()
	{
		$oldLocale = setlocale(LC_ALL, 0);
		if (!setlocale(LC_ALL, 'en_US.iso8859-1') AND !setlocale(LC_ALL, 'English_United States.1252'))
		{
			throw new Exception('setlocale');
		}
		$this->t('abč', "ab\xe4\x8d"); // strtolower
		setlocale(LC_ALL, $oldLocale);
	}

	public function testArray()
	{
		$this->tt(array(
			'id' => 123,
			'text' => 'string',
			'bla_bla' => $object = new Directory,
			'xx_x' => NULL,
			'true' => false,
		), array(
			'id' => 123,
			'text' => 'string',
			'blaBla' => $object,
			'xxX' => NULL,
			'true' => false,
		));
	}

	public function testEmpty()
	{
		$this->tt(array(), array());
		$this->t(NULL, '');
		$this->t(true, '1');
		$this->t(false, '0');
		$this->t('', '');
		$this->t(0, '0');

		$this->tt(array(
			333 => 1,
			NULL => 2,
			false => 3,
			true => 4,
		), array(
			333 => 1,
			NULL => 2,
			false => 3,
			true => 4,
		));
	}

	/**
	 * @covers Orm\SqlConventional::foreignKeyFormat
	 * @covers Orm\SqlConventional::loadFk
	 * @covers Orm\SqlConventional::__construct
	 */
	public function testFk()
	{
		$this->t('aaa_id', 'aaa');
		$this->t('b_b_b_id', 'bBB');
	}

	/** bug s cache */
	public function testCacheBug()
	{
		parent::t('aaa_bbb', 'aaa_bbb');
		$this->t('aaa_bbb', 'aaaBbb');

		parent::t('aaa', 'aaa_id');
		$this->t('aaa', 'aaa');


		$this->t('aaa_id', 'aaa');
		parent::t('aaa_id', 'aaa_id');
	}

	public function testNotSame()
	{
		$this->t(parent::t('AAA', 'a_a_a'), 'aAA');
		parent::t($this->t('_abc', 'Abc'), 'abc');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\SqlConventional', 'formatStorageToEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
