<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Factory for IMapper.
 */
interface IMapperFactory
{

	/**
	 * @param IRepository
	 * @return IMapper
	 */
	public function createMapper(IRepository $repository);

}
