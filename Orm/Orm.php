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

	/** @var string 5.3|5.2 */
	const PACKAGE = '<build::orm>';

}

/*§php52
if (!defined('PHP_VERSION_ID'))
{
	// php < 5.2.7
	$tmp = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2]));
}

php52§*/
require_once __DIR__ . '/Common/Object.php';
require_once __DIR__ . '/Common/ObjectMixin.php';
require_once __DIR__ . '/Common/Callback.php';
require_once __DIR__ . '/Common/Inflector.php';
require_once __DIR__ . '/Common/AnnotationsParser.php';
require_once __DIR__ . '/Common/Exceptions/ExceptionHelper.php';
require_once __DIR__ . '/Common/Exceptions/BadReturnException.php';
require_once __DIR__ . '/Common/Exceptions/DeprecatedException.php';
require_once __DIR__ . '/Common/Exceptions/EntityNotFoundException.php';
require_once __DIR__ . '/Common/Exceptions/InvalidArgumentException.php';
require_once __DIR__ . '/Common/Exceptions/InvalidEntityException.php';
require_once __DIR__ . '/Common/Exceptions/NotImplementedException.php';
require_once __DIR__ . '/Common/Exceptions/NotSupportedException.php';
require_once __DIR__ . '/Common/Exceptions/RequiredArgumentException.php';
require_once __DIR__ . '/Common/Exceptions/MemberAccessException.php';
require_once __DIR__ . '/Common/Exceptions/NotCallableException.php';

require_once __DIR__ . '/RepositoryContainer/IRepositoryContainer.php';
require_once __DIR__ . '/RepositoryContainer/RepositoryContainer.php';
require_once __DIR__ . '/RepositoryContainer/Exceptions/RepositoryNotFoundException.php';
require_once __DIR__ . '/RepositoryContainer/Exceptions/RepositoryAlreadyRegisteredException.php';

require_once __DIR__ . '/Repository/IRepository.php';
require_once __DIR__ . '/Repository/Repository.php';
require_once __DIR__ . '/Repository/Helpers/RepositoryHelper.php';
require_once __DIR__ . '/Repository/Helpers/IdentityMap.php';
require_once __DIR__ . '/Repository/Helpers/PerformanceHelper.php';
require_once __DIR__ . '/Repository/Helpers/MapperAutoCaller.php';
require_once __DIR__ . '/Repository/Helpers/MapperAutoCallerException.php';
require_once __DIR__ . '/Repository/Exceptions/RecursiveException.php';

require_once __DIR__ . '/Repository/Events/Events.php';
require_once __DIR__ . '/Repository/Events/EventArguments.php';
require_once __DIR__ . '/Repository/Events/Types/IListener.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerHydrateBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerHydrateAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerAttach.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistBeforeInsert.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistBeforeUpdate.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersist.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistAfterInsert.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistAfterUpdate.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerPersistAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerRemoveBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerRemoveAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerFlushBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerFlushAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerCleanBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerCleanAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerSerializeBefore.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerSerializeAfter.php';
require_once __DIR__ . '/Repository/Events/Types/IListenerSerializeConventional.php';

require_once __DIR__ . '/Entity/IEntity.php';
require_once __DIR__ . '/Entity/EntityFragments/EventEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/AttachableEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/ValueEntityFragment.php';
require_once __DIR__ . '/Entity/EntityFragments/BaseEntityFragment.php';
require_once __DIR__ . '/Entity/Entity.php';
require_once __DIR__ . '/Entity/Helpers/EntityHelper.php';
require_once __DIR__ . '/Entity/Helpers/ValidationHelper.php';
require_once __DIR__ . '/Entity/Helpers/EntityToArray.php';
require_once __DIR__ . '/Entity/Helpers/EntityToArrayNoModeException.php';
require_once __DIR__ . '/Entity/Exceptions/EntityAlreadyAttachedException.php';
require_once __DIR__ . '/Entity/Exceptions/EntityNotAttachedException.php';
require_once __DIR__ . '/Entity/Exceptions/EntityNotPersistedException.php';
require_once __DIR__ . '/Entity/Exceptions/NotValidException.php';
require_once __DIR__ . '/Entity/Exceptions/PropertyAccessException.php';
require_once __DIR__ . '/Entity/Exceptions/EntityWasRemovedException.php';

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
require_once __DIR__ . '/Entity/MetaData/MetaDataNestingLevelException.php';

require_once __DIR__ . '/Mappers/IMapper.php';
require_once __DIR__ . '/Mappers/Mapper.php';
require_once __DIR__ . '/Mappers/DibiMapper.php';
require_once __DIR__ . '/Mappers/ArrayMapper.php';
require_once __DIR__ . '/Mappers/FileMapper.php';
require_once __DIR__ . '/Mappers/Helpers/DibiPersistenceHelper.php';
require_once __DIR__ . '/Mappers/Helpers/DibiJoinHelper.php';
require_once __DIR__ . '/Mappers/Exceptions/MapperPersistenceException.php';
require_once __DIR__ . '/Mappers/Exceptions/MapperJoinException.php';
require_once __DIR__ . '/Mappers/Exceptions/ArrayMapperLockException.php';

require_once __DIR__ . '/Mappers/Factory/IMapperFactory.php';
require_once __DIR__ . '/Mappers/Factory/MapperFactory.php';
require_once __DIR__ . '/Mappers/Factory/AnnotationClassParser.php';
require_once __DIR__ . '/Mappers/Factory/Exceptions/AnnotationClassParserException.php';
require_once __DIR__ . '/Mappers/Factory/Exceptions/AnnotationClassParserMorePossibleClassesException.php';
require_once __DIR__ . '/Mappers/Factory/Exceptions/AnnotationClassParserNoClassFoundException.php';

require_once __DIR__ . '/Mappers/Conventional/IConventional.php';
require_once __DIR__ . '/Mappers/Conventional/IDatabaseConventional.php';
require_once __DIR__ . '/Mappers/Conventional/NoConventional.php';
require_once __DIR__ . '/Mappers/Conventional/SqlConventional.php';

require_once __DIR__ . '/Mappers/Collection/IEntityCollection.php';
require_once __DIR__ . '/Mappers/Collection/ArrayCollection.php';
require_once __DIR__ . '/Mappers/Collection/BaseDibiCollection.php';
require_once __DIR__ . '/Mappers/Collection/DibiCollection.php';
require_once __DIR__ . '/Mappers/Collection/DataSourceCollection.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/HydrateEntityIterator.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/FetchAssoc.php';
require_once __DIR__ . '/Mappers/Collection/Helpers/FindByHelper.php';

require_once __DIR__ . '/Relationships/IRelationship.php';
require_once __DIR__ . '/Relationships/BaseToMany.php';
require_once __DIR__ . '/Relationships/OneToMany.php';
require_once __DIR__ . '/Relationships/ManyToMany.php';
require_once __DIR__ . '/Relationships/Mappers/IManyToManyMapper.php';
require_once __DIR__ . '/Relationships/Mappers/ArrayManyToManyMapper.php';
require_once __DIR__ . '/Relationships/Mappers/DibiManyToManyMapper.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaData.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataToOne.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataOneToOne.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataManyToOne.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataToMany.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataOneToMany.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipMetaDataManyToMany.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipLoader.php';
require_once __DIR__ . '/Relationships/MetaData/RelationshipLoaderException.php';
require_once __DIR__ . '/Relationships/Exceptions/BadEntityException.php';

require_once __DIR__ . '/DI/IServiceContainer.php';
require_once __DIR__ . '/DI/ServiceContainer.php';
require_once __DIR__ . '/DI/IServiceContainerFactory.php';
require_once __DIR__ . '/DI/ServiceContainerFactory.php';
require_once __DIR__ . '/DI/Exceptions/FrozenContainerException.php';
require_once __DIR__ . '/DI/Exceptions/InvalidServiceFactoryException.php';
require_once __DIR__ . '/DI/Exceptions/ServiceAlreadyExistsException.php';
require_once __DIR__ . '/DI/Exceptions/ServiceNotFoundException.php';
require_once __DIR__ . '/DI/Exceptions/ServiceNotInstanceOfException.php';
