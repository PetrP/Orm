<?php

use Orm\RepositoryContainer;
use Nette\Utils\Html;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::persist
 * @covers Orm\DibiPersistenceHelper
 */
class DibiMapper_persist2_Test extends DibiMapper_Connected_Test
{

	private $e;
	protected function setUp()
	{
		parent::setUp();
		$this->e = new DibiMapper_persist_Entity;
	}

	private function t($v)
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed')), 'dibimapper_connected_dibi');
		$this->d->addExpected('query', true, "INSERT INTO `dibimapper_connected_dibi` (`mixed`) VALUES ($v)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->m->persist($this->e);
	}

	public function testScalarString()
	{
		$this->e->mixed = 'string';
		$this->t("'string'");
	}

	public function testScalarInt()
	{
		$this->e->mixed = 123;
		$this->t(123);
	}

	public function testScalarBool()
	{
		$this->e->mixed = true;
		$this->t(1);
	}

	public function testNull()
	{
		$this->e->mixed = NULL;
		$this->t('NULL');
	}

	public function testDate()
	{
		$this->e->mixed = new DateTime('2011-11-11');
		$this->t("'2011-11-11 00:00:00'");
	}

	public function testArray()
	{
		$this->e->mixed = array('cow', 'boy');
		$this->t("'a:2:{i:0;s:3:\\\"cow\\\";i:1;s:3:\\\"boy\\\";}'");
	}

	public function testArrayObject()
	{
		$this->e->mixed = $a = new ArrayObject(array('cow', 'boy'));
		$this->t("'C:11:\\\"ArrayObject\\\":49:{x:i:0;a:2:{i:0;s:3:\\\"cow\\\";i:1;s:3:\\\"boy\\\";};m:a:0:{}}'");
	}

	public function testArrayObjectSubClass()
	{
		$this->e->mixed = new MyArrayObject(array('cow', 'boy'));
		$this->setExpectedException('Nette\InvalidStateException', 'Neumim ulozit `DibiMapper_persist_Entity::$mixed` MyArrayObject');
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->persist($this->e);
	}

	public function testToString()
	{
		$this->e->mixed = Html::el('b', 'foo');
		$this->t("'<b>foo</b>'");
	}

	public function testEntity()
	{
		$this->e->mixed = $this->m->model->tests->getById(2);
		$this->t(2);
	}

	public function testInjection()
	{
		$this->e->mixed = new ArrayMapper_flush_Injection;
		$this->e->mixed->setInjectedValue(array('foo', 'bar'));
		$this->t("'a:2:{i:0;s:3:\\\"foo\\\";i:1;s:3:\\\"bar\\\";}'");
	}

	public function testBad()
	{
		$this->e->mixed = new ArrayIterator(array());
		$this->setExpectedException('Nette\InvalidStateException', 'Neumim ulozit `DibiMapper_persist_Entity::$mixed` ArrayIterator');
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->persist($this->e);
	}

}
