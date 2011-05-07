<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;

require_once WWW_DIR . '/Model/Generate/GenerateTable.php';

$g = new GenerateTable($model);

/**
 * @property string $string
 * @property int $int
 * @property bool $bool
 * @property DateTime $date
 * @property float $float
 * @property int|NULL $intNull
 * @property array $array
 * @property mixed $conventionalTestBlahBlah
 * @property Test $entity {m:1 Tests}
 * @property TestToTests $entities {1:m}
 * @property Unknown $unknown
 */
class Test extends Entity {}

class TestsRepository extends Repository {}

class Test2 extends Test {}

class Test2sRepository extends Repository {}

class TestToTests extends OneToMany {}

dt($g->getCreateTableSql($model->tests));

$rl = new RobotLoader();
if (method_exists($rl, 'setCacheStorage')) $rl->setCacheStorage(Environment::getService('Nette\\Caching\\ICacheStorage'));
$rl->addDirectory(__FILE__);
dt($g->getAllCreateTablesSql($rl));

__halt_compiler();
------EXPECT------
"
CREATE TABLE `tests` (
	`id` bigint unsigned NOT NULL AUTO_INCREMENT,
	`string` varchar(255) NOT NULL,
	`int` int NOT NULL,
	`bool` tinyint(1) unsigned NOT NULL,
	`date` datetime NOT NULL,
	`float` float NOT NULL,
	`int_null` int NULL,
	`array` text NOT NULL,
	`conventional_test_blah_blah` text NOT NULL,
	`entity_id` bigint unsigned NOT NULL,
	`unknown` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE='InnoDB';
"

"

CREATE TABLE `tests` (
	`id` bigint unsigned NOT NULL AUTO_INCREMENT,
	`string` varchar(255) NOT NULL,
	`int` int NOT NULL,
	`bool` tinyint(1) unsigned NOT NULL,
	`date` datetime NOT NULL,
	`float` float NOT NULL,
	`int_null` int NULL,
	`array` text NOT NULL,
	`conventional_test_blah_blah` text NOT NULL,
	`entity_id` bigint unsigned NOT NULL,
	`unknown` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE='InnoDB';


CREATE TABLE `test2s` (
	`id` bigint unsigned NOT NULL AUTO_INCREMENT,
	`string` varchar(255) NOT NULL,
	`int` int NOT NULL,
	`bool` tinyint(1) unsigned NOT NULL,
	`date` datetime NOT NULL,
	`float` float NOT NULL,
	`int_null` int NULL,
	`array` text NOT NULL,
	`conventional_test_blah_blah` text NOT NULL,
	`entity_id` bigint unsigned NOT NULL,
	`unknown` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE='InnoDB';

"
