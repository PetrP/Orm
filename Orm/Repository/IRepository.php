<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Pracuje z entitamy, nezavisle na konretnim ulozisti.
 * Ukladani, mazani, nacitani entit.
 *
 * Pro kazdou entitu (nebo skupinu pribuznych entit) si vytvorte repository.
 *
 * Konvence je pojmenovavat repository mnoznym cisle, a entity jednotnim.
 *
 * <pre
 * class ArticlesRepository extends Repository
 *  ...
 * </pre>
 *
 * Repository se zizkava pres model {@see RepositoryContainer}
 * <pre>
 *
 * $model; // instanceof RepositoryContainer
 * // instanceof ArticlesRepository
 * $model->articles;
 * </pre>
 *
 * Repository je NEZAVISLE NA KONKRETNIM ULOZISTI.
 * O uloziste se stara Mapper {@see DibiMapper} {@see IMapper}
 *
 * Konvence je pojmenovavat metody na vytahovani dat
 * getBy<...>() pro ziskani jednoho zaznamu
 * findBy<...>() pro ziskani kolekce zaznamu {@see IEntityCollection}
 * findAll() vsechny zaznamy
 *
 * Na mapperu lze volat metody jako mapper->findByAuthorAndTag($author, $tag) atd
 * Ale na repository je potreba si vsechny vytahovaci metody vytvorit.
 * <pre>
 * public function findByAuthor($author)
 * {
 * 	return $this->mapper->findByAuthor($author);
 * }
 * public function getByName($name)
 * {
 * 	return $this->mapper->getByName($name);
 * }
 * </pre>
 *
 *
 * Defaultne se vytvari entita podle repositoryName v jednotnem cisle ArticlesRepository > Article:
 * @see self::getEntityClassName()
 *
 * Defaultne se pouziva `<RepositoryName>Mapper` nebo DibiMapper:
 * @see self::createMapper()
 *
 * @see self::getById() ziskani zaznamu
 * @see self::persist() ukladani
 * @see self::remove() mazani
 * @see self::flush() promitnuti zmen
 * @see self::clean() zruseni zmen
 *
 */
interface IRepository
{

	/**
	 * @param scalar
	 * @return IEntity
	 */
	public function getById($id);

	/**
	 * Zapoji entity do do repository.
	 *
	 * Vola udalosti:
	 * @see Entity::onAttach()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	public function attach(IEntity $entity);

	/**
	 * Ulozit entitu {@see IMapper::persist()} a zapoji ji do repository {@see self::attach()}
	 * Jen kdyz se zmenila {@see Entity::isChanged()}
	 *
	 * Ulozi take vsechny relationship, tedy entity ktere tato entity obsahuje v ruznych vazbach.
	 *
	 * Vola udalosti:
	 * @see Entity::onBeforePersist()
	 * @see Entity::onBeforeUpdate() OR Entity::onBeforeInsert()
	 * @see Entity::onPersist()
	 * @see Entity::onAfterUpdate() OR Entity::onAfterInsert()
	 * @see Entity::onAfterPersist()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	public function persist(IEntity $entity);

	/**
	 * Smaze entitu z uloziste {@see IMapper::remove()} a odpoji ji z repository.
	 * Z entitou lze pak jeste pracovat do ukonceni scriptu, ale uz nema id a neni zapojena na repository.
	 *
	 * Vola udalosti:
	 * @see Entity::onBeforeRemove()
	 * @see Entity::onAfterRemove()
	 *
	 * @param scalar|IEntity
	 * @return bool
	 */
	public function remove($entity);

	/**
	 * Primitne vsechny zmeny do uloziste.
	 * @param bool true jenom pro tuto repository; false pro vsechny repository
	 * @return void
	 * @see IMapper::flush()
	 * @see RepositoryContainer::flush()
	 */
	public function flush($onlyThis = false);

	/**
	 * Zrusi vsechny zmeny, ale do ukonceni scriptu se zmeny porad drzi.
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @param bool true jenom pro tuto repository; false pro vsechny repository
	 * @return void
	 * @see IMapper::clean()
	 * @see RepositoryContainer::clean()
	 */
	public function clean($onlyThis = false);

	/**
	 * Nazev repository. Vetsinou lowercase nazev tridy bez sufixu Repository
	 * @return string
	 */
	public function getRepositoryName();

	/**
	 * Mapper ktery pouziva tato repository.
	 * @see self::createMapper()
	 * @return DibiMapper |IMapper
	 */
	public function getMapper();

	/** @return IRepositoryContainer */
	public function getModel();

	/**
	 * Mozno ovlivnit jake entity repository vyraby.
	 * Pri $data === NULL vraci pole nazvu vsech trid ktere tato repository muze vyrobit,
	 * jinak vraci konkretni nazev tridy pro tyto data.
	 * Kdyz vyraby jen jednu tridu muze pokazde vratit string.
	 *
	 * Defaultne vraci nazev repository v jednotem cisle, ale hloupe jen bez s na konci.
	 * V pripade nepravidelnosti je mozne prepsat tuto metodu, nebo property entityClassName:
	 * <pre>
	 * // CitiesRepository
	 * protected $entityClassName = 'City';
	 * </pre>
	 *
	 * Repository muze vyrabet ruzne entity, muze se rozhodovat na zaklade nejake polozky kterou ma ulozenou v ulozisti, napr. $type
	 * <pre>
	 * // ProductsRepository
	 * public function getEntityClassName(array $data = NULL)
	 * {
	 * 	$entities = array(
	 * 		Product::BOOK => 'Book',
	 * 		Product::MAGAZINE => 'Magazine',
	 * 		Product::CD_MUSIC => 'CdMusic',
	 * 		Product::DVD_MOVIE => 'DvdMovie',
	 * 	);
	 *
	 * 	if ($data === NULL) return $entities;
	 * 	else if (isset($entities[$data['type']])) return $entities[$data['type']];
	 * }
	 *
	 * </pre>
	 *
	 * Do not call directly.
	 * @param array|NULL
	 * @return string|array
	 */
	public function getEntityClassName(array $data = NULL);

	/**
	 * Donacteni parametru do entity.
	 * Do not call directly.
	 * @see Entity::getValue()
	 * @param IEntity
	 * @param string
	 * @return array
	 * @todo refaktorovat
	 */
	public function lazyLoad(IEntity $entity, $param);

	/**
	 * Vytvori mapper pro tuto repository.
	 * Defaultne nacita mapper podle jmena `<RepositoryName>Mapper`.
	 * Jinak DibiMapper.
	 * Pro pouziti vlastniho mapper staci jen vytvorit tridu podle konvence, nebo prepsat tuto metodu.
	 * @return DibiMapper |IMapper
	 * @see self::getMapper()
	 */
	//protected function createMapper();

	/**
	 * Je mozne tuto entitu ulozit do tohoto repository?
	 * @param IEntity
	 * @return bool
	 * @see self::getEntityClassName()
	 */
	public function isEntity(IEntity $entity);

	/**
	 * Vytvori entity, nebo vrati tuto existujici.
	 * Do not call directly.
	 * @internal
	 *
	 * Vola udalosti:
	 * @see Entity::onLoad()
	 *
	 * @param array
	 * @return IEntity
	 * @see self::getEntityClassName()
	 */
	public function createEntity($data);

}
