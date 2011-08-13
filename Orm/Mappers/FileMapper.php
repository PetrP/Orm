<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Utils\SafeStream;

abstract class FileMapper extends ArrayMapper
{

	/** @var bool */
	private static $isStreamRegistered = false;

	/** @param IRepository */
	public function __construct(IRepository $repository)
	{
		parent::__construct($repository);
		if (!self::$isStreamRegistered)
		{
			$wrapers = stream_get_wrappers();
			if (!in_array(SafeStream::PROTOCOL, $wrapers, true))
			{
				// @codeCoverageIgnoreStart
				SafeStream::register();
			}	// @codeCoverageIgnoreEnd
			self::$isStreamRegistered = true;
		}
	}

	/**
	 * Load data from storage
	 * @return array id => array
	 */
	final protected function loadData()
	{
		$path = $this->getFilePath();
		if (!file_exists($path))
		{
			$this->saveData(array());
		}
		return unserialize(file_get_contents('safe://' . $path));
	}

	/**
	 * Save data to storage
	 * @return array id => array
	 * @return void
	 */
	final protected function saveData(array $data)
	{
		file_put_contents('safe://' . $this->getFilePath(), serialize($data));
	}

	/**
	 * Cesta k souboru do ktereho se budou data serializovat
	 * @return string
	 */
	abstract protected function getFilePath();

}
