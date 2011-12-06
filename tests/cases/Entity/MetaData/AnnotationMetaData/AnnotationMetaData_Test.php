<?php

use Orm\MetaData;
use Orm\AnnotationMetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\AnnotationMetaData
 */
class AnnotationMetaData_Test extends TestCase
{
	protected function x($property, $return = NULL)
	{
		return (object) $this->y($property, $return);
	}

	protected function y($property, $return = NULL, $propertyClass = NULL)
	{
		$property = explode(' ', $property, 2);
		if ($property[0]{0} === '@') $property[0] = substr($property[0], 1);
		MockAnnotationMetaData::$mock = array($property[0] => array(@$property[1]));
		$a = MockAnnotationMetaData::getMetaData('AnnotationMetaData_MockEntity', NULL, $propertyClass)->toArray();
		return $return ? $a[$return] : end($a);
	}

	public function testBase()
	{
		$p = $this->x('@property string $blabla');
		$this->assertSame('AnnotationMetaData_MockEntity', $p->since);
		$this->assertSame(array('string' => 'string'), $p->types);
		$this->assertSame(array('method' => NULL), $p->get);
		$this->assertSame(array('method' => NULL), $p->set);
	}

	public function testTypeAfter()
	{
		$this->assertSame($this->y('@property string $blaBla'), $this->y('@property $blaBla string'));
		$this->assertSame($this->y('@property string $blaBla comment'), $this->y('@property $blaBla string comment'));
		$this->assertSame($this->y('@property string|NULL $blaBla'), $this->y('@property $blaBla string|NULL'));
		$this->assertSame($this->y('@property string|NULL $blaBla comment'), $this->y('@property $blaBla string|NULL comment'));
	}

	public function testNoType()
	{
		$this->assertSame($this->y('@property mixed $blabla'), $this->y('@property $blabla'));
	}

	public function testRead()
	{
		$p = $this->x('@property-read $blabla');
		$this->assertSame(array('method' => NULL), $p->get);
		$this->assertSame(NULL, $p->set);
	}

	public function testEmpty()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@property ' in AnnotationMetaData_MockEntity");
		$this->x('@property');
	}

	public function testInvalid()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@property blabla' in AnnotationMetaData_MockEntity");
		$this->x('@property blabla');
	}

	public function testInvalid2()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@property-read blabla' in AnnotationMetaData_MockEntity");
		$this->x('@property-read blabla');
	}

	public function testInvalid3()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@PrOPERTY ' in AnnotationMetaData_MockEntity");
		$this->x('@PrOPERTY');
	}

	public function testInvalid4()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@property-reed \$bla' in AnnotationMetaData_MockEntity");
		$this->x('@property-reed $bla');
	}

	public function testInvalid5()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format '@propurty \$bla' in AnnotationMetaData_MockEntity");
		$this->x('@propurty $bla');
	}

	public function testMacro()
	{
		$p = $this->x('@property $bla {default abc}');
		$this->assertSame('abc', $p->default);
	}

	public function testMacroMulti()
	{
		$p = $this->x('@property $bla {default abc} {enum a,b,c}');
		$this->assertSame('abc', $p->default);
		$this->assertSame(array('a','b','c'), $p->enum['constants']);
	}

	public function testMacroInvalid()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Unknown annotation macro '{xyz}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {xyz}');
	}

	public function testMacroInvalid2()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format, extra curly bracket '{' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {');
	}

	public function testMacroInvalid3()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format, extra curly bracket '}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla }');
	}

	public function testMacroWithCurlyBracked()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format, extra curly bracket '{default {}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {default {}');
	}

	public function testMacroMultiInvalid()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format, extra curly bracket 'default abc}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property mixed $bla default abc} {enum a,b,c}');
	}

	public function testMacroMultiInvalid2()
	{
		$this->setExpectedException('Orm\AnnotationMetaDataException', "Invalid annotation format, extra curly bracket '{default abc' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {default abc {enum a,b,c}');
	}

	public function testAliases()
	{
		new RepositoryContainer;
		$this->assertSame(MetaData::OneToOne, $this->x('@property $bla {1:1 TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToOne, $this->x('@property $bla {m:1 TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToOne, $this->x('@property $bla {n:1 TestEntity}')->relationship);

		$this->assertSame(MetaData::ManyToMany, $this->x('@property $bla {m:m TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property $bla {m:n TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property $bla {n:m TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property $bla {n:n TestEntity}')->relationship);

		$this->assertSame(MetaData::OneToMany, $this->x('@property $bla {1:m TestEntity}')->relationship);
		$this->assertSame(MetaData::OneToMany, $this->x('@property $bla {1:n TestEntity}')->relationship);
	}

	public function testNative()
	{
		$m = AnnotationMetaData::getMetaData('AnnotationMetaData_MockEntity');
		$this->assertInstanceof('Orm\MetaData', $m);
	}

	public function testBC()
	{
		$p = $this->x('@property -read $blabla');
		$this->assertSame(array('method' => NULL), $p->get);
		$this->assertSame(NULL, $p->set);
	}

	public function testMacroNoBuildParam()
	{
		$p = (object) $this->y('@property $bla {abc cba, abc}', NULL, 'AnnotationMetaData_MetaDataProperty');
		$this->assertSame('??cba, abc??', $p->default);
	}

}
