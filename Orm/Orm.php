<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

require_once __DIR__ . '/RepositoryContainer/RepositoryContainer.php';
require_once __DIR__ . '/Mappers/DibiMapper.php';
require_once __DIR__ . '/Mappers/FileMapper.php';
require_once __DIR__ . '/Relationships/OneToMany.php';
require_once __DIR__ . '/Relationships/ManyToMany.php';
require_once __DIR__ . '/Entity/Injection/Injection.php';

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
