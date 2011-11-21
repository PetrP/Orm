<?php

namespace MapperFactory_createMapper_anotation_namespace2;

use Orm\Repository;
use TestsMapper;


/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper1
 */
class Mfcmans2_InAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace2\NotExistsMapper
 */
class Mfcmans2_NotExists_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace2\Mfcmans2_Same_Mapper
 */
class Mfcmans2_Same_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_Same_Mapper extends TestsMapper
{}

class Mfcmans2_ExistsDefault_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_ExistsDefault_Mapper extends TestsMapper
{}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper3
 */
class Mfcmans2_ExistsDefaultAndAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_ExistsDefaultAndAnotation_Mapper extends TestsMapper
{}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper4
 */
class Mfcmans2_OnParent_Parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_OnParent_Repository extends Mfcmans2_OnParent_Parent_Repository
{}

class Mfcmans2_OnSubParent_Parent_Repository extends Mfcmans2_OnParent_Parent_Repository
{}
class Mfcmans2_OnSubParent_Repository extends Mfcmans2_OnSubParent_Parent_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper5
 */
class Mfcmans2_RewriteOnSubParent_Parent_Repository extends Mfcmans2_OnSubParent_Parent_Repository
{}
class Mfcmans2_RewriteOnSubParent_Repository extends Mfcmans2_RewriteOnSubParent_Parent_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper6
 */
class Mfcmans2_RewriteOnSubChild_Repository extends Mfcmans2_RewriteOnSubParent_Parent_Repository
{}

class Mfcmans2_OnParentAndExistsDefault_Repository extends Mfcmans2_OnParent_Parent_Repository
{}
class Mfcmans2_OnParentAndExistsDefault_Mapper extends TestsMapper
{}

class Mfcmans2_ParantHasDefault_Repository extends Mfcmans2_ExistsDefault_Repository
{}

/**
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper8
 * @mapper MapperFactory_createMapper_anotation_namespace\Mfcmans_Mapper9
 */
class Mfcmans2_More_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper
 */
class Mfcmans2_Empty_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

abstract class Mfcmans2_AbstractParantHasDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_AbstractParantHasDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans2_AbstractParantHasDefault_Repository extends Mfcmans2_AbstractParantHasDefault_parent_Repository
{}

abstract class Mfcmans2_AbstractParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class Mfcmans2_AbstractParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans2_AbstractParantHasAbstractDefault_Repository extends Mfcmans2_AbstractParantHasAbstractDefault_parent_Repository
{}

class Mfcmans2_ParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class Mfcmans2_ParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans2_ParantHasAbstractDefault_Repository extends Mfcmans2_ParantHasAbstractDefault_parent_Repository
{}

/**
 * @mapper false
 */
class Mfcmans2_HasDefaultButFalse_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans2_HasDefaultButFalse_Mapper extends TestsMapper
{}

/**
 * @mapper false
 */
class Mfcmans2_HasDefaultButFalseJumpToParent_Repository extends Mfcmans2_InAnotation_Repository
{}
class Mfcmans2_HasDefaultButFalseJumpToParent_Mapper extends TestsMapper
{}
