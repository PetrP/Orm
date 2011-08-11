<?php

/**
 * @covers Orm\AnnotationClassParser::get
 * @covers Orm\AnnotationClassParser::defaultClassFallback
 * @covers Orm\AnnotationClassParser::getByClassName
 * @covers Orm\AnnotationClassParser::getByReflection
 */
class AnnotationClassParser_get_Test extends TestCase
{
	private $p;

	protected function setUp()
	{
		$this->p = new AnnotationClassParser_get_AnnotationClassParser;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\AnnotationClassParserException', "parser 'test' is not registered");
		$this->p->get('test', (object) array());
	}

	public function testNoObject()
	{
		$this->p->register('test', 'Countable');
		$this->setExpectedException('Orm\AnnotationClassParserException', "expected object, array given");
		$this->p->get('test', array());
	}

	public function testBadInterface()
	{
		$this->p->register('test', 'Countable');
		$this->setExpectedException('Orm\AnnotationClassParserException', "'stdClass' is not instance of Countable");
		$this->p->get('test', (object) array());
	}

	public function testNoClassFound()
	{
		$this->p->register('test', 'Traversable');
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', "ArrayObject::@test no class found");
		$this->p->get('test', new ArrayObject);
	}

	public function testOk()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				'ArrayObject',
			),
		);
		$this->assertSame('ArrayObject', $this->p->get('test', new ArrayObject));
	}

	public function testClassNotFound()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				'FooBar',
			),
		);
		$this->setExpectedException('Orm\AnnotationClassParserException', "ArrayObject::@test class 'FooBar' not exists");
		$this->p->get('test', new ArrayObject);
	}

	public function testMoreInAnnotation()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				'FooBar',
				'FooBar',
			),
		);
		$this->setExpectedException('Orm\AnnotationClassParserException', "Cannot redeclare ArrayObject::@test");
		$this->p->get('test', new ArrayObject);
	}

	public function testNoString()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				true,
			),
		);
		$this->setExpectedException('Orm\AnnotationClassParserException', "ArrayObject::@test expected class name, boolean given");
		$this->p->get('test', new ArrayObject);
	}

	public function testOkFallback()
	{
		$this->p->register('test', 'Traversable', function () {
			return 'ArrayObject';
		});
		$this->assertSame('ArrayObject', $this->p->get('test', new ArrayObject));
	}

	public function testFallbackAndAnnotationIsSame()
	{
		$this->p->register('test', 'Traversable', function () {
			return 'ArrayObject';
		});
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				'ArrayObject',
			),
		);
		$this->assertSame('ArrayObject', $this->p->get('test', new ArrayObject));
	}

	public function testFallbackAndAnnotationIsNotSame()
	{
		$this->p->register('test', 'Traversable', function () {
			return 'stdClass';
		});
		$this->p->a['ArrayObject'] = array(
			'test' => array(
				'ArrayObject',
			),
		);
		$this->setExpectedException('Orm\AnnotationClassParserMorePossibleClassesException', "Exists annotation ArrayObject::@test and fallback 'stdClass'");
		$this->p->get('test', new ArrayObject);
	}

	public function testFallbackNotExists()
	{
		$this->p->register('test', 'Traversable', function () {
			return 'FooBar';
		});
		$this->p->a['ArrayObject'] = array('test' => array('ArrayObject'));
		$this->assertSame('ArrayObject', $this->p->get('test', new ArrayObject));
	}

	public function testFindInNamespace1()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$this->p->register('test', 'Traversable');
		$this->p->a['AnnotationClassParser_get_ns\ArrayObject'] = array(
			'test' => array(
				'ArrayObject',
			),
		);
		$class = 'AnnotationClassParser_get_ns\ArrayObject';
		$this->assertSame('AnnotationClassParser_get_ns\ArrayObject', $this->p->get('test', new $class));
	}

	public function testFindInNamespace2()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$this->p->register('test', 'Traversable');
		$this->p->a['AnnotationClassParser_get_ns\ArrayObject'] = array(
			'test' => array(
				'stdClass',
			),
		);
		$class = 'AnnotationClassParser_get_ns\ArrayObject';
		$this->assertSame('stdClass', $this->p->get('test', new $class));
	}

	public function testOnParent()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['ArrayObject'] = array('test' => array('ArrayObject'));
		$this->assertSame('ArrayObject', $this->p->get('test', new AnnotationClassParser_get_ArrayObject));
	}

	public function testOnParentNoImplement()
	{
		$this->p->register('test', 'SplObserver');
		$this->p->a['ArrayObject'] = array('test' => array('ArrayObject'));
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', "AnnotationClassParser_get_ArrayObject_SplObserver::@test no class found");
		$this->p->get('test', new AnnotationClassParser_get_ArrayObject_SplObserver);
	}

	public function testOnParentNotInstantiableAnnotation()
	{
		$this->p->register('test', 'Traversable');
		$this->p->a['AnnotationClassParser_get_ArrayObject_Abstract'] = array('test' => array('ArrayObject'));
		$this->assertSame('ArrayObject', $this->p->get('test', new AnnotationClassParser_get_ArrayObject_ParentAbstract));
	}

	public function testOnParentFallback()
	{
		$this->p->register('test', 'Traversable', function ($class) {
			if ($class === 'ArrayObject')
			{
				return 'ArrayObject';
			}
		});
		$this->assertSame('ArrayObject', $this->p->get('test', new AnnotationClassParser_get_ArrayObject));
	}

	public function testOnParentNotInstantiableFallback()
	{
		$this->p->register('test', 'Traversable', function ($class) {
			if ($class === 'AnnotationClassParser_get_ArrayObject_Abstract')
			{
				return 'ArrayObject';
			}
		});
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', "AnnotationClassParser_get_ArrayObject_ParentAbstract::@test no class found");
		$this->p->get('test', new AnnotationClassParser_get_ArrayObject_ParentAbstract);
	}

	public function testOnParentFallbackNotInstantiable()
	{
		$this->p->register('test', 'Traversable', function ($class) {
			if ($class === 'ArrayObject')
			{
				return 'AnnotationClassParser_get_ArrayObject_Abstract';
			}
		});
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', "AnnotationClassParser_get_ArrayObject::@test no class found");
		$this->p->get('test', new AnnotationClassParser_get_ArrayObject);
	}

	public function testHasDefaultButFalse()
	{
		$this->p->register('test', 'Traversable', function ($class) {
			return 'ArrayObject';
		});
		$this->p->a['ArrayObject'] = array('test' => array(false));
		$this->setExpectedException('Orm\AnnotationClassParserNoClassFoundException', 'ArrayObject::@test no class found');
		$this->p->get('test', new ArrayObject);
	}

	public function testHasDefaultButFalseJumpToParent()
	{
		$this->p->register('test', 'Traversable', function ($class) {
			return $class;
		});
		$this->p->a['AnnotationClassParser_get_ArrayObject'] = array('test' => array(false));
		$this->assertSame('ArrayObject', $this->p->get('test', new AnnotationClassParser_get_ArrayObject));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AnnotationClassParser', 'get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
