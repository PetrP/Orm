<?php

use Orm\Repository;

class MapperFactory_createMapper_anotation_Mapper1 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper3 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper4 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper5 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper6 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper8 extends TestsMapper {}
class MapperFactory_createMapper_anotation_Mapper9 extends TestsMapper {}


/**
 * @mapper MapperFactory_createMapper_anotation_Mapper1
 */
class MapperFactory_createMapper_anotation_InAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper NotExistsMapper
 */
class MapperFactory_createMapper_anotation_NotExists_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper MapperFactory_createMapper_anotation_Same_Mapper
 */
class MapperFactory_createMapper_anotation_Same_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_Same_Mapper extends TestsMapper
{}

class MapperFactory_createMapper_anotation_ExistsDefault_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_ExistsDefault_Mapper extends TestsMapper
{}

/**
 * @mapper MapperFactory_createMapper_anotation_Mapper3
 */
class MapperFactory_createMapper_anotation_ExistsDefaultAndAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_ExistsDefaultAndAnotation_Mapper extends TestsMapper
{}

/**
 * @mapper MapperFactory_createMapper_anotation_Mapper4
 */
class MapperFactory_createMapper_anotation_OnParent_Parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_OnParent_Repository extends MapperFactory_createMapper_anotation_OnParent_Parent_Repository
{}

class MapperFactory_createMapper_anotation_OnSubParent_Parent_Repository extends MapperFactory_createMapper_anotation_OnParent_Parent_Repository
{}
class MapperFactory_createMapper_anotation_OnSubParent_Repository extends MapperFactory_createMapper_anotation_OnSubParent_Parent_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_Mapper5
 */
class MapperFactory_createMapper_anotation_RewriteOnSubParent_Parent_Repository extends MapperFactory_createMapper_anotation_OnSubParent_Parent_Repository
{}
class MapperFactory_createMapper_anotation_RewriteOnSubParent_Repository extends MapperFactory_createMapper_anotation_RewriteOnSubParent_Parent_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_Mapper6
 */
class MapperFactory_createMapper_anotation_RewriteOnSubChild_Repository extends MapperFactory_createMapper_anotation_RewriteOnSubParent_Parent_Repository
{}

class MapperFactory_createMapper_anotation_OnParentAndExistsDefault_Repository extends MapperFactory_createMapper_anotation_OnParent_Parent_Repository
{}
class MapperFactory_createMapper_anotation_OnParentAndExistsDefault_Mapper extends TestsMapper
{}

class MapperFactory_createMapper_anotation_ParantHasDefault_Repository extends MapperFactory_createMapper_anotation_ExistsDefault_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_Mapper8
 * @mapper MapperFactory_createMapper_anotation_Mapper9
 */
class MapperFactory_createMapper_anotation_More_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper
 */
class MapperFactory_createMapper_anotation_Empty_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

abstract class MapperFactory_createMapper_anotation_AbstractParantHasDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_AbstractParantHasDefault_parent_Mapper extends TestsMapper
{}
class MapperFactory_createMapper_anotation_AbstractParantHasDefault_Repository extends MapperFactory_createMapper_anotation_AbstractParantHasDefault_parent_Repository
{}

abstract class MapperFactory_createMapper_anotation_AbstractParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class MapperFactory_createMapper_anotation_AbstractParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class MapperFactory_createMapper_anotation_AbstractParantHasAbstractDefault_Repository extends MapperFactory_createMapper_anotation_AbstractParantHasAbstractDefault_parent_Repository
{}

class MapperFactory_createMapper_anotation_ParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class MapperFactory_createMapper_anotation_ParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class MapperFactory_createMapper_anotation_ParantHasAbstractDefault_Repository extends MapperFactory_createMapper_anotation_ParantHasAbstractDefault_parent_Repository
{}

/**
 * @mapper false
 */
class MapperFactory_createMapper_anotation_HasDefaultButFalse_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class MapperFactory_createMapper_anotation_HasDefaultButFalse_Mapper extends TestsMapper
{}

/**
 * @mapper false
 */
class MapperFactory_createMapper_anotation_HasDefaultButFalseJumpToParent_Repository extends MapperFactory_createMapper_anotation_InAnotation_Repository
{}
class MapperFactory_createMapper_anotation_HasDefaultButFalseJumpToParent_Mapper extends TestsMapper
{}
