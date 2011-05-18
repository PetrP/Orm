<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers AnnotationMetaData
 */
class AnnotationMetaData_Test extends TestCase
{
	protected function x($property, $return = NULL)
	{
		$property = explode(' ', $property, 2);
		if ($property[0]{0} === '@') $property[0] = substr($property[0], 1);
		MockAnnotationMetaData::$mock = array($property[0] => array(@$property[1]));
		$a = MockAnnotationMetaData::getMetaData('AnnotationMetaData_MockEntity')->toArray();
		return (object) ($return ? $a[$return] : end($a));
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
		$this->assertEquals($this->x('@property string $blaBla'), $this->x('@property $blaBla string'));
		$this->assertEquals($this->x('@property string $blaBla comment'), $this->x('@property $blaBla string comment'));
		$this->assertEquals($this->x('@property string|NULL $blaBla'), $this->x('@property $blaBla string|NULL'));
		$this->assertEquals($this->x('@property string|NULL $blaBla comment'), $this->x('@property $blaBla string|NULL comment'));
	}

	public function testNoType()
	{
		$this->assertEquals($this->x('@property mixed $blabla'), $this->x('@property $blabla'));
	}

	public function testRead()
	{
		$p = $this->x('@property-read $blabla');
		$this->assertSame(array('method' => NULL), $p->get);
		$this->assertSame(NULL, $p->set);
	}

	public function testEmpty()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@property ' in AnnotationMetaData_MockEntity");
		$this->x('@property');
	}

	public function testInvalid()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@property blabla' in AnnotationMetaData_MockEntity");
		$this->x('@property blabla');
	}

	public function testInvalid2()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@property-read blabla' in AnnotationMetaData_MockEntity");
		$this->x('@property-read blabla');
	}

	public function testInvalid3()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@PrOPERTY ' in AnnotationMetaData_MockEntity");
		$this->x('@PrOPERTY');
	}

	public function testInvalid4()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@property-reed \$bla' in AnnotationMetaData_MockEntity");
		$this->x('@property-reed $bla');
	}

	public function testInvalid5()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format '@propurty \$bla' in AnnotationMetaData_MockEntity");
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
		$this->setExpectedException('InvalidStateException', "Unknown annotation macro '{xyz}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {xyz}');
	}

	public function testMacroInvalid2()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format, extra curly bracket '{' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {');
	}

	public function testMacroInvalid3()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format, extra curly bracket '}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla }');
	}

	public function testMacroWithCurlyBracked()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format, extra curly bracket '{default {}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {default {}');
	}

	public function testMacroMultiInvalid()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format, extra curly bracket 'default abc}' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property mixed $bla default abc} {enum a,b,c}');
	}

	public function testMacroMultiInvalid2()
	{
		$this->setExpectedException('InvalidStateException', "Invalid annotation format, extra curly bracket '{default abc' in AnnotationMetaData_MockEntity::\$bla");
		$this->x('@property $bla {default abc {enum a,b,c}');
	}

	public function testAliases()
	{
		new Model;
		$this->assertSame(MetaData::OneToOne, $this->x('@property $bla {1:1 TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToOne, $this->x('@property $bla {m:1 TestEntity}')->relationship);
		$this->assertSame(MetaData::ManyToOne, $this->x('@property $bla {n:1 TestEntity}')->relationship);

		$this->assertSame(MetaData::ManyToMany, $this->x('@property AnnotationMetaData_ManyToMany $bla {m:m}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property AnnotationMetaData_ManyToMany $bla {m:n}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property AnnotationMetaData_ManyToMany $bla {n:m}')->relationship);
		$this->assertSame(MetaData::ManyToMany, $this->x('@property AnnotationMetaData_ManyToMany $bla {n:n}')->relationship);

		$this->assertSame(MetaData::OneToMany, $this->x('@property AnnotationMetaData_OneToMany $bla {1:m}')->relationship);
		$this->assertSame(MetaData::OneToMany, $this->x('@property AnnotationMetaData_OneToMany $bla {1:n}')->relationship);
	}

	public function testNative()
	{
		$m = AnnotationMetaData::getMetaData('AnnotationMetaData_MockEntity');
		$this->assertInstanceof('MetaData', $m);
	}

	public function testBC()
	{
		$p = $this->x('@property -read $blabla');
		$this->assertSame(array('method' => NULL), $p->get);
		$this->assertSame(NULL, $p->set);
	}

}