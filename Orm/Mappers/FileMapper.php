<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Provides mapping between repository and storage.
 * All entities are serialize to one file via Nette\Utils\SafeStream protocol.
 *
 * @see self::getFilePath()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers
 */
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
			if (!in_array('safe', $wrapers, true))
			{
				throw new NotSupportedException("Stream 'safe' is not registered; use Nette\\Utils\\SafeStream::register().");
			}
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
	 * @param array id => array
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
