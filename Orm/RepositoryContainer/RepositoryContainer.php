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
use Dibi;

require_once __DIR__ . '/../Orm.php';
require_once __DIR__ . '/IRepositoryContainer.php';
require_once __DIR__ . '/../Entity/Entity.php';
require_once __DIR__ . '/../Repository/Repository.php';
require_once __DIR__ . '/../Mappers/Mapper.php';
require_once __DIR__ . '/../DI/ServiceContainer.php';
require_once __DIR__ . '/../Mappers/Factory/AnnotationClassParser.php';
require_once __DIR__ . '/../Mappers/Factory/MapperFactory.php';

/**
 * Kolekce Repository.
 * Stara se o jejich vytvareni.
 * Je to vstupni bod do modelu z jinych casti aplikace.
 *
 * Na repository se pristupuje jako k property:
 * <pre>
 *
 * $orm = new RepositoryContainer;
 *
 * $orm->articles; // instanceof ArticlesRepository
 *
 * $article = $orm->articles->getById(1); // instanceof Article
 *
 * </pre>
 *
 * Do aplikace orm vsunete napriklad takto:
 * <pre>
 * // config.ini
 * service.orm = RepositoryContainer
 *
 * // BasePresenter.php
 * /**
 *  * @property-read RepositoryContainer $orm
 *  *⁄
 * abstract class BasePresenter extends Presenter
 * {
 * 	/** @var RepositoryContainer *⁄
 * 	private $orm;
 *
 *  /** @return RepositoryContainer *⁄
 * 	public function getOrm()
 * 	{
 * 		if (!isset($this->orm))
 * 		{
 * 			$this->orm = $this->context->getService('orm');
 * 		}
 * 		return $this->orm;
 * 	}
 * }
 * </pre>
 */
class RepositoryContainer extends Object implements IRepositoryContainer
{
	/** @var RepositoryContainer @deprecated @see self::get() */
	static private $instance;

	/** @var array repositoryClass => IRepository */
	private $repositories = array();

	/** @var array repositoryAlias => repositoryClass */
	private $aliases = array();

	/** @var IServiceContainer */
	private $container;

	public function __construct()
	{
		self::$instance = $this;
		$this->container = new ServiceContainer;
		$this->container->addService('annotationClassParser', 'Orm\AnnotationClassParser');
		$this->container->addService('mapperFactory', function (\Orm\IServiceContainer $container) {
			return new \Orm\MapperFactory($container->getService('annotationClassParser', 'Orm\AnnotationClassParser'));
		});
		$this->container->addService('repositoryHelper', 'Orm\RepositoryHelper');
		$this->container->addService('dibi', function () {
			return dibi::getConnection();
		});
	}

	/** @return IServiceContainer */
	public function getContext()
	{
		return $this->container;
	}

	/**
	 * @param IServiceContainer
	 * @return IRepositoryContainer
	 */
	public function setContext(IServiceContainer $container)
	{
		$this->container = $container;
		return $this;
	}

	/**
	 * @deprecated
	 * Vraci posledni vytvoreny container, je pro zpetnou kompatibilitu.
	 * Entity::getModel(NULL)
	 * @return RepositoryContainer
	 */
	public static function get()
	{
		if (!isset(self::$instance))
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
	 * @var string repositoryClassName|alias
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
			$old = $this->getRepositoryClass($name, true);
			if (!class_exists($name) AND class_exists($old))
			{
				// bc
				$this->checkRepositoryClass($old, $name, true, $originalClass);
			}
			else
			{
				$this->checkRepositoryClass($name, $name, true, $originalClass);
			}
			$class = $this->aliases[$name] = $originalClass;
		}
		if (!isset($this->repositories[$class]))
		{
			$this->container->freeze();
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
	 * @param string repositoryClassName|alias
	 * @return bool
	 */
	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->aliases[$name])) return true;
		if ($this->checkRepositoryClass($name, $name, false, $originClass))
		{
			$this->aliases[$name] = $originClass;
			return true;
		}
		if ($this->checkRepositoryClass($this->getRepositoryClass($name, true), $name, false, $originClass))
		{
			// bc
			$this->aliases[$name] = $originClass;
			return true;
		}
		return false;
	}

	/**
	 * Je tato trida repository?
	 * @param string repositoryClass
	 * @param string repositoryClassName
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
	 * @deprecated proto final; je pro bc, neda se s nim upravit chovani
	 * repositoryName > repositoryClass
	 * @param string repositoryName
	 * @return string repositoryClass
	 */
	final protected function getRepositoryClass($name)
	{
		if (func_num_args() < 2 OR func_get_arg(1) !== true)
		{
			throw new DeprecatedException('Orm\RepositoryContainer::getRepositoryClass() is deprecated; repositoryName is deprecated; use class name instead');
		}
		if (!$name) return NULL;
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);
		return $class;
	}

	/**
	 * <pre>
	 * $orm->articles;
	 * </pre>
	 * Do not call directly.
	 * @param string repositoryClassName|alias
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
