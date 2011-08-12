<?php

abstract class FetchAssoc_Base_Test extends TestCase
{

	protected $e;

	protected function setUp()
	{
		$this->e = array(
			new ArrayCollection_Entity(2, 'a'),
			new ArrayCollection_Entity(1, 'b'),
			new ArrayCollection_Entity(3, 'a'),
			new ArrayCollection_Entity(4, 'b'),
		);
	}

}
