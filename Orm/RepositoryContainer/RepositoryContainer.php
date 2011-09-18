<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Nette\DeprecatedException;
use ReflectionClass;

require_once __DIR__ . '/../Orm.php';
require_once __DIR__ . '/IRepositoryContainer.php';
require_once __DIR__ . '/../Entity/Entity.php';
require_once __DIR__ . '/../Repository/Repository.php';
require_once __DIR__ . '/../Mappers/Mapper.php';

/**
 * Collection of IRepository.
 *
 * Cares of initialisation repository.
 * It is entry point into model from other parts of application.
 * Stores container of services what other objects may need.
 *
 * Repository can be accessed like to property:
 * <code>
 *
 * $orm = new RepositoryContainer;
 *
 * $orm->articles; // instanceof ArticlesRepository
 *
 * $article = $orm->articles->getById(1); // instanceof Article
 *
 * </code>
 *
 * Nette application can reach orm like that:
 * <code>
 * // config.ini
 * service.orm = RepositoryContainer
 *
 * // BasePresenter.php
 * /**
 *  * @property-read RepositoryContainer $orm
 *  * /
 * abstract class BasePresenter extends Presenter
 * {
 * 	/** @var RepositoryContainer * /
 * 	private $orm;
 *
 * 	/** @return RepositoryContainer * /
 * 	public function getOrm()
 * 	{
 * 		if ($this->orm === NULL)
 * 		{
 * 			$this->orm = $this->context->getService('orm');
 * 		}
 * 		return $this->orm;
 * 	}
 * }
 * </code>
 */
class RepositoryContainer extends Object implements IRepositoryContainer
{
	/** @var RepositoryContainer @deprecated @see self::get() */
	static private $instance;

	/** @var array repositoryClass => IRepository */
	private $repositories = array();

	/** @var array repositoryAlias => repositoryClass */
	private $aliases = array();

	public function __construct()
	{
		self::$instance = $this;
	}

	/**
	 * @deprecated
	 * Vraci posledni vytvoreny container, je pro zpetnou kompatibilitu.
	 * Entity::getModel(NULL)
	 * @return RepositoryContainer
	 */
	public static function get()
	{
		if (self::$instance === NULL)
		{
			throw new InvalidStateException('RepositoryContainer hasn\'t been instanced yet.');
		}
		if (func_num_args() === 0 OR func_get_arg(0) !== NULL)
		{
			throw new DeprecatedException('RepositoryContainer::get() is deprecated do not use it.');
		}
		return self::$instance;
	}

	/**
	 * Dej mi instanci repository.
	 * @param string repositoryName|alias
	 * @return Repository |IRepository
	 */
	public function getRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->aliases[$name]))
		{
			$class = $this->aliases[$name];
		}
		else
		{
			$this->checkRepositoryClass($this->getRepositoryClass($name), $name, true, $originalClass);
			$class = $this->aliases[$name] = $originalClass;
		}
		if (!isset($this->repositories[$class]))
		{
			$this->repositories[$class] = new $class($this);
		}
		return $this->repositories[$class];
	}

	/**
	 * Registruje repository pod jednodusim nazvem
	 * @param string
	 * @param string
	 * @return IRepositoryContainer
	 */
	public function register($alias, $repositoryClass)
	{
		$this->checkRepositoryClass($repositoryClass, $repositoryClass, true, $originClass);
		$alias = strtolower($alias);
		if ($this->isRepository($alias))
		{
			throw new InvalidStateException("Repository alias '{$alias}' is already registered");
		}
		$this->aliases[$alias] = $originClass;
		return $this;
	}

	/**
	 * Existuje repository pod timto nazvem?
	 * @param string repositoryName
	 * @return bool
	 */
	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->aliases[$name])) return true;
		if ($this->checkRepositoryClass($this->getRepositoryClass($name), $name, false, $originClass))
		{
			$this->aliases[$name] = $originClass;
			return true;
		}
		return false;
	}

	/**
	 * Je tato trida repository?
	 * @param string repositoryClass
	 * @param string repositoryName
	 * @param bool
	 * @param string origin class name
	 * @return true or throw exception
	 * @throws InvalidStateException
	 */
	final private function checkRepositoryClass($class, $name, $throw = true, & $originClass = NULL)
	{
		if (!class_exists($class))
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$name}' doesn't exists");
		}

		$reflection = new ReflectionClass($class);
		$originClass = $reflection->getName();

		if (!$reflection->implementsInterface('Orm\IRepository'))
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$originClass}' must implement Orm\\IRepository");
		}
		else if ($reflection->isAbstract())
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$originClass}' is abstract.");
		}
		else if (!$reflection->isInstantiable())
		{
			if (!$throw) return false;
			throw new InvalidStateException("Repository '{$originClass}' isn't instantiable");
		}

		return true;
	}

	/**
	 * repositoryName > repositoryClass
	 * @param string repositoryName
	 * @return string repositoryClass
	 */
	protected function getRepositoryClass($name)
	{
		if (!$name) return NULL;
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);
		return $class;
	}

	/**
	 * <code>
	 * $orm->articles;
	 * </code>
	 * Do not call directly.
	 * @param string repositoryName
	 * @return IRepository
	 */
	public function & __get($name)
	{
		$r = $this->getRepository($name);
		return $r;
	}

	/**
	 * Promitne vsechny zmeny do uloziste na vsech repository.
	 * @return void
	 * @see IRepository::flush()
	 * @see IMapper::flush()
	 */
	public function flush()
	{
		foreach ($this->repositories as $repo)
		{
			$repo->flush(true);
		}
	}

	/**
	 * Zrusi vsechny zmeny na vsech repository, ale do ukonceni scriptu se zmeny porad drzi.
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @return void
	 * @see IRepository::clean()
	 * @see IMapper::clean()
	 */
	public function clean()
	{
		foreach ($this->repositories as $repo)
		{
			$repo->clean(true);
		}
	}

}
