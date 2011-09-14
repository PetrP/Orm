<?php

/**
 * @covers Orm\SqlConventional::formatEntityToStorage
 * @covers Orm\SqlConventional::storageFormat
 */
class SqlConventional_formatEntityToStorage_Test extends TestCase
{
	protected $c;
	protected function setUp()
	{
		$this->c = new MockSqlConventional;
	}

	protected function t($entry, $expected)
	{
		$tmp = (string) key($this->c->formatEntityToStorage(array($entry => NULL)));
		$this->assertSame($expected, $tmp);
		return $tmp;
	}

	protected function tt(array $entry, array $expected)
	{
		$this->assertSame($expected, $this->c->formatEntityToStorage($entry));
	}

	public function test()
	{
		$this->t('id', 'id');
		$this->t('sameThing', 'same_thing');
		$this->t('same_thing', 'same_thing');
		$this->t('same1Thing', 'same1_thing');
		$this->t('ABC', 'a_b_c');
		$this->t('123', '123');
		$this->t('1bC', '1b_c');
		$this->t('sameThingSameThing', 'same_thing_same_thing');
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
			'blaBla' => $object = new Directory,
			'XxX' => NULL,
			'True' => false,
		), array(
			'id' => 123,
			'text' => 'string',
			'bla_bla' => $object,
			'xx_x' => NULL,
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
		$this->t('aaa', 'aaa_id');
		$this->t('bBB', 'b_b_b_id');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\SqlConventional', 'formatEntityToStorage');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
