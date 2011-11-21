<?php

namespace MapperFactory_createMapper_anotation_namespace;

use Orm\Repository;
use TestsMapper;

class Mfcmans_Mapper1 extends TestsMapper {}
class Mfcmans_Mapper3 extends TestsMapper {}
class Mfcmans_Mapper4 extends TestsMapper {}
class Mfcmans_Mapper5 extends TestsMapper {}
class Mfcmans_Mapper6 extends TestsMapper {}
class Mfcmans_Mapper8 extends TestsMapper {}
class Mfcmans_Mapper9 extends TestsMapper {}


/**
 * @mapper Mfcmans_Mapper1
 */
class Mfcmans_InAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper NotExistsMapper
 */
class Mfcmans_NotExists_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper Mfcmans_Same_Mapper
 */
class Mfcmans_Same_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_Same_Mapper extends TestsMapper
{}

class Mfcmans_ExistsDefault_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_ExistsDefault_Mapper extends TestsMapper
{}

/**
 * @mapper Mfcmans_Mapper3
 */
class Mfcmans_ExistsDefaultAndAnotation_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_ExistsDefaultAndAnotation_Mapper extends TestsMapper
{}

/**
 * @mapper Mfcmans_Mapper4
 */
class Mfcmans_OnParent_Parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_OnParent_Repository extends Mfcmans_OnParent_Parent_Repository
{}

class Mfcmans_OnSubParent_Parent_Repository extends Mfcmans_OnParent_Parent_Repository
{}
class Mfcmans_OnSubParent_Repository extends Mfcmans_OnSubParent_Parent_Repository
{}

/**
 * @mapper Mfcmans_Mapper5
 */
class Mfcmans_RewriteOnSubParent_Parent_Repository extends Mfcmans_OnSubParent_Parent_Repository
{}
class Mfcmans_RewriteOnSubParent_Repository extends Mfcmans_RewriteOnSubParent_Parent_Repository
{}

/**
 * @mapper Mfcmans_Mapper6
 */
class Mfcmans_RewriteOnSubChild_Repository extends Mfcmans_RewriteOnSubParent_Parent_Repository
{}

class Mfcmans_OnParentAndExistsDefault_Repository extends Mfcmans_OnParent_Parent_Repository
{}
class Mfcmans_OnParentAndExistsDefault_Mapper extends TestsMapper
{}

class Mfcmans_ParantHasDefault_Repository extends Mfcmans_ExistsDefault_Repository
{}

/**
 * @mapper Mfcmans_Mapper8
 * @mapper Mfcmans_Mapper9
 */
class Mfcmans_More_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

/**
 * @mapper
 */
class Mfcmans_Empty_Repository extends Repository
{protected $entityClassName = 'TestEntity';}

abstract class Mfcmans_AbstractParantHasDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_AbstractParantHasDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans_AbstractParantHasDefault_Repository extends Mfcmans_AbstractParantHasDefault_parent_Repository
{}

abstract class Mfcmans_AbstractParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class Mfcmans_AbstractParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans_AbstractParantHasAbstractDefault_Repository extends Mfcmans_AbstractParantHasAbstractDefault_parent_Repository
{}

class Mfcmans_ParantHasAbstractDefault_parent_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
abstract class Mfcmans_ParantHasAbstractDefault_parent_Mapper extends TestsMapper
{}
class Mfcmans_ParantHasAbstractDefault_Repository extends Mfcmans_ParantHasAbstractDefault_parent_Repository
{}

/**
 * @mapper false
 */
class Mfcmans_HasDefaultButFalse_Repository extends Repository
{protected $entityClassName = 'TestEntity';}
class Mfcmans_HasDefaultButFalse_Mapper extends TestsMapper
{}

/**
 * @mapper false
 */
class Mfcmans_HasDefaultButFalseJumpToParent_Repository extends Mfcmans_InAnotation_Repository
{}
class Mfcmans_HasDefaultButFalseJumpToParent_Mapper extends TestsMapper
{}
