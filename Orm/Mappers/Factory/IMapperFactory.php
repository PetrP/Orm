<?php

namespace Orm;

interface IMapperFactory
{

	/**
	 * @param IRepository
	 * @return IMapper
	 */
	public function createMapper(IRepository $repository);

}
