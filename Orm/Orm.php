<?php

namespace Orm;

require_once __DIR__ . '/RepositoryContainer/RepositoryContainer.php';
require_once __DIR__ . '/Mappers/DibiMapper.php';
require_once __DIR__ . '/Mappers/FileMapper.php';
require_once __DIR__ . '/Relationships/OneToMany.php';
require_once __DIR__ . '/Relationships/ManyToMany.php';
require_once __DIR__ . '/Entity/Injection/Injection.php';

final class Orm
{

	const VERSION = '<build::version>';

	const VERSION_ID = /*<build::version_id>*/0/**/;

	const REVISION = '<build::revision> released on <build::date>';

	const PACKAGE = '<build::orm> for Nette <build::nette>';

}
