<?php

namespace Orm;

interface IManyToManyMapper
{

	function add(IEntity $parent, array $ids);

	function remove(IEntity $parent, array $ids);

	function load(IEntity $parent);

}
