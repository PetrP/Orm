<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Dibi;
use DibiConnection;
use DibiTranslator;
use ReflectionMethod;
use stdClass;

/**
 * Provides mapping between repository and storage.
 * It maps to database table via DibiConnection.
 *
 * @property-read DibiConnection $connection
 * @property-read IDatabaseConventional $conventional
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers
 */
class DibiMapper extends Mapper
{

	/** @var DibiConnection @see self::getConnection() */
	private $connection;

	/** @var DibiJoinHelper @see self::getJoinInfo() */
	private $joinHelper;

	/** @var array @see self::begin() */
	private static $transactions = array();

	/** @var string @see self::getTableName() */
	private $tableName;

	/** @var string @see self::getPrimaryKey() */
	private $primaryKey;

	/**
	 * Vsechny entity.
	 * Musi vratit skutecne vsechny entity.
	 * Zadna jina metoda nesmi vratit nejakou entitu kterou by nevratila tato.
	 * @return IEntityCollection
	 */
	public function findAll()
	{
		list($class, $classInfo) = $this->getCollectionClass(true);
		if ($classInfo === 'dibi')
		{
			return new $class($this->getTableName(), $this->getConnection(), $this->getRepository());
		}
		else if ($classInfo === 'datasource')
		{
			return $this->dataSource($this->getTableName());
		}
		else
		{
			throw new NotImplementedException;
		}
	}

	/**
	 * @param scalar
	 * @return IEntity|NULL
	 * @todo vynocovat na IMapper?
	 */
	public function getById($id)
	{
		if ($id === NULL) return NULL;
		return $this->findAll()->getBy(array('id' => $id));
	}

	/**
	 * @see IRepository::persist()
	 * @param IEntity
	 * @return scalar id
	 */
	public function persist(IEntity $entity)
	{
		$this->begin();
		return $this->getPersistenceHelper()->persist($entity);
	}

	/**
	 * @see IRepository::remove()
	 * @param IEntity
	 * @return bool
	 */
	public function remove(IEntity $entity)
	{
		$this->begin();
		return (bool) $this->getConnection()->delete($this->getTableName())->where('%n = %s', $this->getPrimaryKey(), $entity->id)->execute();
	}

	/**
	 * @see IRepository::flush()
	 * @return void
	 */
	public function flush()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (isset(self::$transactions[$hash]))
		{
			$connection->commit();
			unset(self::$transactions[$hash]);
		}
	}

	/**
	 * @see IRepository::clean()
	 * @return void
	 */
	public function rollback()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (isset(self::$transactions[$hash]))
		{
			$connection->rollback();
			// todo zmeny zustanou v Repository::$entities
			unset(self::$transactions[$hash]);
		}
	}

	/**
	 * Vytvori mapperu pro m:m asociaci
	 * @see ManyToMany::getMapper()
	 * @param string Nazev parametru na entite ktera patri repository tohodle mapperu
	 * @param IRepository Repository na kterou asociace ukazuje
	 * @param string Parametr na druhe strane kam asociace ukazuje
	 * @return IManyToManyMapper
	 * <code>
	 *
	 *	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam)
	 *	{
	 *		$mapper = parent::createManyToManyMapper($param, $targetRepository, $targetParam);
	 *		if ($param === 'comments')
	 *		{
	 *			$mapper->parentParam = 'user_id';
	 *			$mapper->childParam = 'comment_id';
	 *		}
	 *		return $mapper;
	 *	}
	 *
	 * </code>
	 */
	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam)
	{
		$mapper = new DibiManyToManyMapper($this->getConnection());
		$c = $this->getConventional();
		$mapper->table = $c->getManyToManyTable($this->getRepository(), $targetRepository);
		$mapper->parentParam = $c->getManyToManyParam($targetParam);
		$mapper->childParam = $c->getManyToManyParam($param);
		return $mapper;
	}

	/**
	 * @see self::createConnection()
	 * @return DibiConnection
	 */
	final public function getConnection()
	{
		if ($this->connection === NULL)
		{
			$connection = $this->createConnection();
			if (!($connection instanceof DibiConnection))
			{
				throw new BadReturnException(array($this, 'createConnection', 'DibiConnection', $connection));
			}
			$this->connection = $connection;
		}
		return $this->connection;
	}

	/**
	 * @see self::createConventional()
	 * @return IDatabaseConventional
	 */
	final public function getConventional()
	{
		return parent::getConventional('Orm\IDatabaseConventional');
	}

	/**
	 * @see self::getConnection()
	 * @return DibiConnection
	 */
	protected function createConnection()
	{
		return $this->getModel()->getContext()->getService('dibi', 'DibiConnection');
	}

	/**
	 * @see self::getConventional()
	 * @return IDatabaseConventional
	 */
	protected function createConventional()
	{
		return new SqlConventional($this);
	}

	/**
	 * @see self::persist()
	 * @return DibiPersistenceHelper
	 */
	protected function getPersistenceHelper()
	{
		return new DibiPersistenceHelper($this->getConnection(), $this->getConventional(), $this->getTableName(), $this->getRepository()->getEvents());
	}

	/**
	 * Zahaji transakci.
	 * Vola se pri kazde operaci. Jen pri prvnim zavolani se transakce otevira.
	 * @see self::persist()
	 * @see self::remove()
	 */
	protected function begin()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (!isset(self::$transactions[$hash]))
		{
			$connection->begin();
			self::$transactions[$hash] = true;
		}
	}

	/**
	 * <code>
	 * 	$mapper->dataSource('SELECT foo, bar FROM table WHERE [bar] = %i', 3);
	 * </code>
	 * @param string
	 * @return DataSourceCollection
	 */
	protected function dataSource()
	{
		list($class, $classInfo) = $this->getCollectionClass(true);
		if ($class === 'Orm\DibiCollection')
		{
			// bc, i kdyz se pouziva DibiCollection tak dataSource muze fungovat, kdyz se nepouziva custom collection
			$class = 'Orm\DataSourceCollection';
		}
		else if ($classInfo !== 'datasource')
		{
			throw new NotSupportedException();
		}

		static $dibiTranslatorVersion; // pro bc se starsi dibi
		if ($dibiTranslatorVersion === NULL)
		{
			$dibiTranslatorVersion = 'driver';
			$r = new ReflectionMethod('DibiTranslator', '__construct');
			$p = $r->getParameters();
			if (current($p)->name === 'connection')
			{
				$dibiTranslatorVersion = 'connection';
			}
		}

		$connection = $this->getConnection();
		$connection->getDriver(); // v novem dibi se tady connectne
		if (!$connection->isConnected())
		{
			// @codeCoverageIgnoreStart
			$connection->sql(''); // protoze nema public metodu DibiConnection::connect()
		}	// @codeCoverageIgnoreEnd
		$translator = new DibiTranslator($dibiTranslatorVersion === 'connection' ? $connection : $connection->getDriver());
		$args = func_get_args();
		return new $class($translator->translate($args), $connection, $this->getRepository());
	}

	/**
	 * Vraci nazev tridy kterou tento mapper pouziva jako IEntityCollection
	 * @see self::getCollectionClass()
	 * @return string
	 */
	protected function createCollectionClass()
	{
		return 'Orm\DibiCollection';
	}

	/**
	 * Nazev tabulky
	 * @return string
	 * @see self::$tableName
	 * @see IDatabaseConventional::getTable()
	 */
	protected function getTableName()
	{
		if ($this->tableName === NULL)
		{
			$this->tableName = $this->getConventional()->getTable($this->getRepository());
		}
		return $this->tableName;
	}

	/**
	 * Primarni klic
	 * @return string
	 * @see self::$primaryKey
	 * @see IDatabaseConventional::getPrimaryKey()
	 */
	final protected function getPrimaryKey()
	{
		if ($this->primaryKey === NULL)
		{
			$this->primaryKey = $this->getConventional()->getPrimaryKey();
		}
		return $this->primaryKey;
	}

	/**
	 * @internal
	 * @param string author->lastName or author->group->name
	 * @return object
	 * 	->key author.last_name
	 *  ->joins[] array(
	 * 		alias => author
	 * 	?	sourceKey => author
	 * 		xConventionalKey => author_id
	 * 		yConventionalKey => id
	 * 		table => users
	 * 		findBy => array
	 * 	?	mapper => DibiMapper
	 * 	?	conventional => IDatabaseConventional
	 * )
	 * Work with all propel defined association.
	 */
	public function getJoinInfo($key, stdClass $result = NULL)
	{
		if (strpos($key, '->') === false)
		{
			return NULL;
		}
		if ($this->joinHelper === NULL)
		{
			$this->joinHelper = new DibiJoinHelper($this);
		}
		$joinInfo = $this->joinHelper->get($key, $result);
		foreach ($joinInfo->joins as & $join)
		{
			if ($join['table'] === NULL)
			{
				$join['table'] = $join['mapper']->getTableName();
			}
		}
		return $joinInfo;
	}
}
