<?php
/**
 * DibiDataSource pro mysql.
 *
 * @version 0.2.3
 * @author Petr Procházka (petr@petrp.cz)
 * @link http://petrp.cz/dibidatasource-pro-mysql
 * @license "New" BSD License
 */

require_once dirname(__FILE__) . '/DataSourceX.php';

/**
 * Automaticky registruje extension method.
 * @author Petr Procházka
 */
class DataSourceXObjectExtensions
{

	/** @var array Callbacky ktere se zavolaji. */
	private $extensions = array();

	/** Register extensions. */
	static public function register()
	{
		static $isRegistered = 0;
		if ($isRegistered++) return;

		$oe = new self;
		foreach (get_class_methods($oe) as $method)
			if (strpos($method, '_') !== false) $oe->extensions[strtr($method, array('_' => '::'))] = array($oe, $method);

		foreach ($oe->extensions as $name => $callback)
		{
			if (strpos($name, '::') !== false)
			{
				$className = substr($name, 0, strpos($name, '::'));
				callback("$className::extensionMethod")->invoke($name, $callback);
			}
			else
			{
				callback($callback)->invoke();
			}
		}
	}

	/**
	 * @param array
	 * @param DibiConnection
	 * @return DibiDataSourceX
	 */
	protected function dataSourceX(array $args, DibiConnection $connection)
	{
		$connection->driver; // nova verze dibi se pripojuje pri getDriver
		if (!$connection->isConnected())
		{
			$connection->sql(''); // stara nepripojuje a nema metodu DibiConnection::connect()
		}
		$translator = new DibiTranslator($connection->driver);
		return new DibiDataSourceX($translator->translate($args), $connection);
	}

	/**
	 * @param DibiConnection
	 * @param array|mixed
	 * @return DibiDataSourceX
	 */
	public function DibiConnection_dataSourceX(DibiConnection $connection, $args)
	{
		$args = func_get_args(); array_shift($args);
		return $this->dataSourceX($args, $connection);
	}

	/**
	 * @param DibiDataSource
	 * @return DibiDataSourceX
	 */
	public function DibiDataSource_toDataSourceX(DibiDataSource $dataSource)
	{
		return $this->dataSourceX(array($dataSource->__toString()), $dataSource->getConnection());
	}

	/**
	 * @param DibiFluent
	 * @return DibiDataSourceX
	 * @todo DibiFluent neumoznuje extensionMethod, protoze prepisuje __call
	 */
	public function DibiFluent_toDataSourceX(DibiFluent $fluent)
	{
		return $this->dataSourceX(array($fluent->__toString()), $fluent->getConnection());
	}

}


DataSourceXObjectExtensions::register();
