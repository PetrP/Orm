<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Naming conventions in storage.
 * Different names of keys between entity and storage.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Conventional
 */
interface IConventional
{

	/**
	 * Prejmenuje klice z entity do formatu uloziste
	 * @param array|Traversable
	 * @return array
	 */
	function formatEntityToStorage($data);

	/**
	 * Prejmenuje klice z uloziste do formatu entity
	 * @param array|Traversable
	 * @return array
	 */
	function formatStorageToEntity($data);

}
