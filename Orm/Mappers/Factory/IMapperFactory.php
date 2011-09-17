<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Factory for IMapper.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Factory
 */
interface IMapperFactory
{

	/**
	 * @param IRepository
	 * @return IMapper
	 */
	public function createMapper(IRepository $repository);

}
