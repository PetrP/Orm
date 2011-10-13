<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ArrayAccess;
use IteratorAggregate;

require_once __DIR__ . '/../Orm.php';

/**
 * Entity
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity
 */
interface IEntity extends ArrayAccess, IteratorAggregate
{

	/** Kdyz parametr obrahuje tuto hodnotu tak se pouzije jeho defaultni hodnota */
	const DEFAULT_VALUE = "\0";

	/**
	 * Entity prevadi na array, je mozne nastavit co udelat z asociacemi.
	 * @see EntityToArray
	 * @param int EntityToArray::*
	 * @return array
	 */
	public function toArray($mode = EntityToArray::AS_IS);

	/**
	 * Nastavuje parametry.
	 * Kdyz neexistuje parametr:
	 * vola setter `set<Param>` kdyz existuje takova methoda a je public;
	 * plni property `$param` kdyz existuje a je public.
	 * @param array|Traversable $values
	 * @return Entity $this
	 */
	public function setValues($values);

	/**
	 * Existuje tento parametr?
	 * Mozno i zjisti jestli je pro cteni/zapis.
	 * @param string
	 * @param int|NULL MetaData::READWRITE MetaData::READ MetaData::WRITE
	 * @return bool
	 */
	public function hasParam($name, $mode = NULL);

	/**
	 * Do not call directly!
	 * Vola urcitou udalost.
	 * @internal
	 * @param string nazev udalosti
	 * @param IRepository
	 * @param array|scalar $data (onLoad) or $id (onPersist)
	 */
	public function fireEvent($method, IRepository $repository = NULL, $more = NULL);

	/**
	 * Repository ktery se o tuto entitu stara.
	 * Existuje jen kdyz entita byla persistovana.
	 * @param bool
	 * @throws InvalidStateException
	 * @return IRepository |NULL
	 */
	public function getRepository($need = true);

	/**
	 * @param bool
	 * @return IRepositoryContainer
	 */
	public function getModel($need = true);

	/**
	 * Byla zmenena nejaka hodnota na teto entite od posledniho ulozeni?
	 * @param string|NULL
	 * @return bool
	 * @see self::markAsChanged()
	 */
	public function isChanged($name = NULL);

	/**
	 * Nastavit, ze tato entita byla zmenena.
	 * @param string|NULL
	 * @return IEntity $this
	 * @see self::isChanged()
	 */
	public function markAsChanged($name = NULL);

	/**
	 * Vytvori MetaData
	 * @param string|IEntity class name or object
	 * @return MetaData
	 */
	public static function createMetaData($entityClass);


	/** Vytvorena nova entita */
	//protected function onCreate();

	/**
	 * Vytazena z mapperu
	 * @param IRepository
	 * @param array
	 */
	//protected function onLoad(IRepository $repository, array $data);

	/**
	 * Pripojeno na repository
	 * @param IRepository
	 */
	//protected function onAttach(IRepository $repository);

	/**
	 * Pred persistovanim (insert nebo update)
	 * @param IRepository
	 */
	//protected function onBeforePersist(IRepository $repository);

	/**
	 * Po persistovani (insert nebo update)
	 * @param IRepository
	 */
	//protected function onAfterPersist(IRepository $repository);

	/**
	 * Behem persistovani, vsechny subentity nemusi byt jeste persistovany
	 * @param IRepository
	 * @param scalar
	 */
	//protected function onPersist(IRepository $repository, $id);

	/**
	 * Pred vymazanim
	 * @param IRepository
	 */
	//protected function onBeforeRemove(IRepository $repository);

	/**
	 * Po vymazani
	 * @param IRepository
	 */
	//protected function onAfterRemove(IRepository $repository);

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	//protected function onBeforeUpdate(IRepository $repository);

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	//protected function onAfterUpdate(IRepository $repository);

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	//protected function onBeforeInsert(IRepository $repository);

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	//protected function onAfterInsert(IRepository $repository);

	/**
	 * Pouziva se pouze ve vlastnich geterech.
	 * Vrati hodnotu parametru, ale nepouzije getter
	 * @param string
	 * @param bool
	 * @return mixed
	 */
	//protected function getValue($name, $need = true);

	/**
	 * Pouziva se pouze ve vlastnich seterech.
	 * Nastavi hodnotu parametru, ale nepouzije setter
	 * @param string
	 * @param mixed
	 * @return Entity $this
	 */
	//protected function setValue($name, $value)

	/**
	 * Nastavi read-only property
	 * @param string
	 * @param mixed
	 * @return Entity $this
	 */
	//protected function setReadOnlyValue($name, $value)
}
