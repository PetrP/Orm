<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\MapperFactory::createMapper
 */
class MapperFactory_createMapper_anotation_namaspace2_Test extends TestCase
{

	private function t($c, $m)
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = "MapperFactory_createMapper_anotation_namespace2\\Mfcmans2_{$c}_Repository";
		if (is_numeric($m)) $m = "MapperFactory_createMapper_anotation_namespace\\Mfcmans_Mapper{$m}";
		$r = new $c(new RepositoryContainer);
		$this->assertSame($m, get_class($r->getMapper()));
	}

	public function testInAnotation()
	{
		$this->t('InAnotation', 1);
	}

	public function testNotExists()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "MapperFactory_createMapper_anotation_namespace2\\Mfcmans2_NotExists_Repository::@mapper class 'MapperFactory_createMapper_anotation_namespace2\\NotExistsMapper' not exists");
		$this->t('NotExists', '?');
	}

	public function testSame()
	{
		$this->t('Same', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_Same_Mapper');
	}

	public function testExistsDefault()
	{
		$this->t('ExistsDefault', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_ExistsDefault_Mapper');
	}

	public function testExistsDefaultAndAnotation()
	{
		$this->setExpectedException('Orm\AnnotationClassParserMorePossibleClassesException', "Exists annotation MapperFactory_createMapper_anotation_namespace2\\Mfcmans2_ExistsDefaultAndAnotation_Repository::@mapper and fallback 'MapperFactory_createMapper_anotation_namespace2\\Mfcmans2_ExistsDefaultAndAnotation_Mapper'");
		$this->t('ExistsDefaultAndAnotation', '?');
	}

	public function testOnParent()
	{
		$this->t('OnParent', '4');
	}

	public function testOnSubParent()
	{
		$this->t('OnSubParent', '4');
	}

	public function testRewriteOnSubParent()
	{
		$this->t('RewriteOnSubParent', '5');
	}

	public function testRewriteOnSubChild()
	{
		$this->t('RewriteOnSubChild', '6');
	}

	public function testOnParentAndExistsDefault()
	{
		$this->t('OnParentAndExistsDefault', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_OnParentAndExistsDefault_Mapper');
	}

	public function testParantHasDefault()
	{
		$this->t('ParantHasDefault', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_ExistsDefault_Mapper');
	}

	public function testMore()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException');
		$this->t('More', '?');
	}

	public function testEmpty()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException');
		$this->t('Empty', '?');
	}

	public function testAbstractParantHasDefault()
	{
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_AbstractParantHasDefault_Repository::@mapper no class found');
		$this->t('AbstractParantHasDefault', '?');
	}

	public function testAbstractParantHasAbstractDefault()
	{
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_AbstractParantHasAbstractDefault_Repository::@mapper no class found');
		$this->t('AbstractParantHasAbstractDefault', '?');
	}

	public function testParantHasAbstractDefault()
	{
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_ParantHasAbstractDefault_Repository::@mapper no class found');
		$this->t('ParantHasAbstractDefault', '?');
	}

	public function testHasDefaultButFalse()
	{
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'MapperFactory_createMapper_anotation_namespace2\Mfcmans2_HasDefaultButFalse_Repository::@mapper no class found');
		$this->t('HasDefaultButFalse', '?');
	}

	public function testHasDefaultButFalseJumpToParent()
	{
		$this->t('HasDefaultButFalseJumpToParent', 1);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MapperFactory', 'createMapper');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
