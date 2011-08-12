<?php

use Orm\Entity;

/**
 * @property $new
 * @property $newByPropertySet
 * @property $newByPropertyGet
 * @property $old
 * @property $withoutMethod
 *
 * @property $exception
 * @property $noParentSet
 * @property $noParentGet
 *
 * @property $callOther
 * @property $callOther2
 */
class ValueEntityFragment_gettersetter_Test_Entity extends Entity
{
	public $setNewCount = 0;
	public $setNewByPropertySetCount = 0;
	public $setOldCount = 0;
	public $setExceptionCount = 0;
	public $setNoParentSetCount = 0;
	public $setCallOtherCount = 0;
	public $setCallOther2Count = 0;

	public $getNewCount = 0;
	public $getNewByPropertyGetCount = 0;
	public $getOldCount = 0;
	public $getExceptionCount = 0;
	public $getNoParentGetCount = 0;
	public $getCallOtherCount = 0;
	public $getCallOther2Count = 0;

	public function setNew($set)
	{
		$this->{__FUNCTION__.'Count'}++;
		return parent::setNew($set);
	}

	public function getNew()
	{
		$this->{__FUNCTION__.'Count'}++;
		return parent::getNew();
	}

	public function setNewByPropertySet($set)
	{
		$this->{__FUNCTION__.'Count'}++;
		$this->newByPropertySet = $set;
		return $this;
	}

	public function getNewByPropertyGet()
	{
		$this->{__FUNCTION__.'Count'}++;
		return @$this->newByPropertyGet;
	}

	public function setOld($set)
	{
		$this->{__FUNCTION__.'Count'}++;
		return $this->setValue('old', $set);
	}

	public function getOld()
	{
		$this->{__FUNCTION__.'Count'}++;
		return $this->getValue('old');
	}

	public $throw = false;
	public function setException($set)
	{
		$this->{__FUNCTION__.'Count'}++;
		if ($this->throw) throw new ValueEntityFragment_setter_Test_Exception();
		return parent::setException($set);
	}

	public function getException()
	{
		$this->{__FUNCTION__.'Count'}++;
		if ($this->throw) throw new ValueEntityFragment_getter_Test_Exception();
		return parent::getException();;
	}

	public function setNoParentSet($set)
	{
		$this->{__FUNCTION__.'Count'}++;
	}

	public function getNoParentGet()
	{
		$this->{__FUNCTION__.'Count'}++;
	}

	public function setCallOther($set)
	{
		$this->{__FUNCTION__.'Count'}++;

		$this->new = $set;
		$this->setOld($set);
		$this->withoutMethod = $set;
		$this->setNoParentGet($set);
		$this->setNoParentSet($set);

		return parent::setCallOther($set);
	}

	public function getCallOther()
	{
		$this->{__FUNCTION__.'Count'}++;

		$this->new;
		$this->getOld();
		$this->withoutMethod;
		$this->getNoParentGet();
		$this->getNoParentSet();

		return parent::getCallOther();
	}

	public function setCallOther2($set)
	{
		$this->{__FUNCTION__.'Count'}++;

		$this->setNew($set);
		$this->old = $set;
		$this->setWithoutMethod($set);
		$this->noParentGet = $set;
		$this->noParentSet = $set;

		return parent::setCallOther2($set);
	}

	public function getCallOther2()
	{
		$this->{__FUNCTION__.'Count'}++;

		$this->getNew();
		$this->old;
		$this->getWithoutMethod();
		$this->noParentGet;
		$this->noParentSet;

		return parent::getCallOther2();
	}
}

class ValueEntityFragment_getter_Test_Exception extends Exception {}
class ValueEntityFragment_setter_Test_Exception extends Exception {}
