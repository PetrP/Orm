<?php
require_once 'c:/www/boot.php';
Environment::setVariable('tempDir', __DIR__ . '/tmp');
$r = new RobotLoader;
$r->addDirectory('c:/www/projects/Model/Model');
$r->addDirectory('c:/www/projects/Model/tests/compare');
$r->addDirectory('c:/www/projectsfork/doctrine2/Doctrine');
$r->autoRebuild = true;
$r->register();

require_once 'c:/www/projects/DataSourceX/DataSourceX/extension.php';

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
// ...
// připravíme si Doctrine konfiguraci
$config = new Configuration;
// informace o entitách budeme brát z anotací v uvedeném adresáři
$metadata = $config->newDefaultAnnotationDriver(__DIR__ . '/d/entity');
$config->setMetadataDriverImpl($metadata);
// nastavení namespace a adresáře pro proxy třídy
$config->setProxyNamespace('CompareTest\Proxy');
$config->setProxyDir(__DIR__ . '/d/proxy');
// připravíme si konfiguraci databázového připojení
$database = array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'dbname' => 'test',
		'user' => 'root',
		'password' => '',
);
// vytvoříme instanci entity manageru
$em = EntityManager::create($database, $config);
/** @var \Doctrine\ORM\EntityRepository */
$prd = $em->getRepository('PersonD');

dibi::connect(array(
		'driver' => 'mysqli',
		'host' => 'localhost',
		'database' => 'test',
		'user' => 'root',
		'password' => '',
));

class Model extends AbstractModel
{
}

/**
 * @property string $name
 * @property int $age
 * @property string $letter
 */
class Person extends Entity
{

}

class PersonsRepository extends Repository
{

}

$prm = $model->persons;


Debug::timer();
$prd->find(153546);
d(Debug::timer());
$prm->getById(153546)->toArray();
d(Debug::timer());

echo '=======';

Debug::timer();
$prd->find(153546);
d(Debug::timer());
$prm->getById(153546)->toArray();
d(Debug::timer());

echo '=======';

Debug::timer();
$prd->find(153547);
d(Debug::timer());
$prm->getById(153547)->toArray();
d(Debug::timer());

echo '=======';

Debug::timer();
count($prd->findByLetter(0));
d(Debug::timer());
count($prm->findByLetter(0));
d(Debug::timer());

echo '=======';

Debug::timer();
foreach ($prd->findByLetter(0) as $e)
{
	(array) $e;
}
d(Debug::timer());
foreach ($prm->findByLetter(0)->fetchAll() as $e)
{
	$e->toArray();
}
d(Debug::timer());
