<?php
/**
 * @Entity
 * @Table(name="persons")
 */
class PersonD
{
	/**
	 * @Column(type="integer")
	 * @Id
	 */
	private $id;

	/** @Column(type="string") */
	private $name;

	/** @Column(type="integer") */
	private $age;

	/** @Column(type="string") */
	private $letter;
}
