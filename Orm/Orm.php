<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Orm.
 * @author Petr Procházka
 * @package Orm
 */
final class Orm
{

	/** @var string <generation>.<major>.<minor> */
	const VERSION = '<build::version>';

	/** @var int <generation> * 10000 + <major> * 100 + <minor> */
	const VERSION_ID = /*<build::version_id>*/0/**/;

	/** @var string <gitCommitHash> released on <date> */
	const REVISION = '<build::revision> released on <build::date>';

	/** @var string 5.3|5.2 for Nette (5.2 without prefixes|5.3) */
	const PACKAGE = '<build::orm> for Nette <build::nette>';

}

/*§php52
/**
 * Simulate closure scope in php 5.2
 * <code>
 * 	function () use ($foo, $bar) {}
 * </code>
 * <code>
 * 	create_function('', 'extract(OrmClosureFix::$vars['.OrmClosureFix::uses(array('foo'=>$foo,'bar'=>$bar)).'], EXTR_REFS);')
 * </code>
 * @see Orm\Builder\PhpParser::replaceClosures()
 * /
class OrmClosureFix
{

	/** @var array Simulate closure scope in php 5.2 @access private * /
	static $vars = array();

	/**
	 * @access private
	 * @param array
	 * @return int
	 * /
	static function uses($args)
	{
		self::$vars[] = $args;
		return count(self::$vars)-1;
	}
}

if (!defined('PHP_VERSION_ID'))
{
	// php < 5.2.7
	$tmp = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2]));
}
php52§*/

require_once __DIR__ . '/Common/ExceptionHelper.php';
require_once __DIR__ . '/Common/BadReturnException.php';
require_once __DIR__ . '/Common/DeprecatedException.php';
require_once __DIR__ . '/Common/EntityNotFoundException.php';
require_once __DIR__ . '/Common/InvalidArgumentException.php';
require_once __DIR__ . '/Common/InvalidEntityException.php';
require_once __DIR__ . '/Common/NotImplementedException.php';
require_once __DIR__ . '/Common/NotSupportedException.php';
require_once __DIR__ . '/Common/RequiredArgumentException.php';

require_once __DIR__ . '/RepositoryContainer/IRepositoryContainer.php';
require_once __DIR__ . '/RepositoryContainer/RepositoryContainer.php';
require_once __DIR__ . '/RepositoryContainer/RepositoryNotFoundException.php';
require_once __DIR__ . '/RepositoryContainer/RepositoryAlreadyRegisteredException.php';

require_once __DIR__ . '/Repository/IRepository.php';
require_once __DIR__ . '/Repository/Repository.php';
require_once __DIR__ . '/Repository/RepositoryHelper.php';
require_once __DIR__ . '/Repository/PerformanceHelper.php';

require_once __DIR__ . '/Entity/IEntity.php';
require_once __DIR__ . '/Entity/EntityFragments/EventEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/AttachableEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/ValueEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/BaseEntityFragment.php';
require_once __DIR__ . '/Entity/Entity.php';
require_once __DIR__ . '/Entity/EntityHelper.php';
require_once __DIR__ . '/Entity/ValidationHelper.php';
require_once __DIR__ . '/Entity/EntityToArray.php';
require_once __DIR__ . '/Entity/EntityToArrayNoModeException.php';
require_once __DIR__ . '/Entity/EntityFragments/EntityNotAttachedException.php';
require_once __DIR__ . '/Entity/EntityFragments/EntityNotPersistedException.php';
require_once __DIR__ . '/Entity/EntityFragments/NotValidException.php';
require_once __DIR__ . '/Entity/EntityFragments/PropertyAccessException.php';

require_once __DIR__ . '/Entity/Injection/IEntityInjection.php';
require_once __DIR__ . '/Entity/Injection/IEntityInjectionLoader.php';
require_once __DIR__ . '/Entity/Injection/IEntityInjectionStaticLoader.php';
require_once __DIR__ . '/Entity/Injection/InjectionFactory.php';
require_once __DIR__ . '/Entity/Injection/Injection.php';

require_once __DIR__ . '/Entity/MetaData/MetaData.php';
require_once __DIR__ . '/Entity/MetaData/MetaDataProperty.php';
require_once __DIR__ . '/Entity/MetaData/AnnotationMetaData.php';
require_once __DIR__ . '/Entity/MetaData/MetaDataException.php';
require_once __DIR__ . '/Entity/MetaData/AnnotationMetaDataException.php';

require_once __DIR__ . '/Mappers/IMapper.php';
require_once __DIR__ . '/Mappers/Mapper.php';
require_once __DIR__ . '/Mappers/DibiMapper.php';
require_once __DIR__ . '/Mappers/ArrayMapper.php';
require_once __DIR__ . '/Mappers/FileMapper.php';
require_once __DIR__ . '/Mappers/DibiPersistenceHelper.php';
require_once __DIR__ . '/Mappers/DibiJoinHelper.php';
require_once __DIR__ . '/Mappers/MapperPersistenceException.php';
require_once __DIR__ . '/Mappers/MapperJoinException.php';
require_once __DIR__ . '/Mappers/ArrayMapperLockException.php';

require_once __DIR__ . '/Mappers/Factory/IMapperFactory.php';
require_once __DIR__ . '/Mappers/Factory/MapperFactory.php';
require_once __DIR__ . '/Mappers/Factory/AnnotationClassParser.php';
require_once __DIR__ . '/Mappers/Factory/AnnotationClassParserException.php';
require_once __DIR__ . '/Mappers/Factory/AnnotationClassParserMorePossibleClassesException.php';
require_once __DIR__ . '/Mappers/Factory/AnnotationClassParserNoClassFoundException.php';

require_once __DIR__ . '/Mappers/Conventional/IConventional.php';
require_once __DIR__ . '/Mappers/Conventional/IDatabaseConventional.php';
require_once __DIR__ . '/Mappers/Conventional/NoConventional.php';
require_once __DIR__ . '/Mappers/Conventional/SqlConventional.php';

require_once __DIR__ . '/Mappers/Collection/IEntityCollection.php';
require_once __DIR__ . '/Mappers/Collection/ArrayCollection.php';
require_once __DIR__ . '/Mappers/Collection/BaseDibiCollection.php';
require_once __DIR__ . '/Mappers/Collection/DibiCollection.php';
require_once __DIR__ . '/Mappers/Collection/DataSourceCollection.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/EntityIterator.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/FetchAssoc.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/FindByHelper.php';

require_once __DIR__ . '/Relationships/IRelationship.php';
require_once __DIR__ . '/Relationships/BaseToMany.php';
require_once __DIR__ . '/Relationships/OneToMany.php';
require_once __DIR__ . '/Relationships/ManyToMany.php';
require_once __DIR__ . '/Relationships/IManyToManyMapper.php';
require_once __DIR__ . '/Relationships/ArrayManyToManyMapper.php';
require_once __DIR__ . '/Relationships/DibiManyToManyMapper.php';
require_once __DIR__ . '/Relationships/RelationshipLoader.php';
require_once __DIR__ . '/Relationships/RelationshipLoaderException.php';
require_once __DIR__ . '/Relationships/bc1m.php';
require_once __DIR__ . '/Relationships/bcmm.php';

require_once __DIR__ . '/DI/IServiceContainer.php';
require_once __DIR__ . '/DI/ServiceContainer.php';
require_once __DIR__ . '/DI/IServiceContainerFactory.php';
require_once __DIR__ . '/DI/ServiceContainerFactory.php';
require_once __DIR__ . '/DI/FrozenContainerException.php';
require_once __DIR__ . '/DI/InvalidServiceFactoryException.php';
require_once __DIR__ . '/DI/ServiceAlreadyExistsException.php';
require_once __DIR__ . '/DI/ServiceNotFoundException.php';
require_once __DIR__ . '/DI/ServiceNotInstanceOfException.php';
