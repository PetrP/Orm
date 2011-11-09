<?php

use Orm\Builder\VersionInfo;

/**
 * @covers Orm\Builder\VersionInfo
 */
class VersionInfo_Test extends TestCase
{
	public function testNoTagDev()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, true);
		$this->assertSame('v0.0.0-dev0', $i->tag);
		$this->assertSame('0.0.0-dev0', $i->version);
		$this->assertSame(-1, $i->versionId);
		$this->assertSame('2011-11-11', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function testNoTagNoDev()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$this->setExpectedException('Exception', 'There is no version tag; Add one or add dev parametr to url: run.php?dev');
		$i = new VersionInfo($git, false);
	}

	public function testTagDev1()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, true);
		$this->assertSame('v0.2.2-dev', $i->tag);
		$this->assertSame('0.2.2-dev', $i->version);
		$this->assertSame(-1, $i->versionId);
		$this->assertSame('2011-09-21', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function testTagDev2()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, true);
		$this->assertSame('v0.2.1-dev', $i->tag);
		$this->assertSame('0.2.1-dev', $i->version);
		$this->assertSame(-1, $i->versionId);
		$this->assertSame('2011-09-06', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function testTagNoDev1()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, false);
		$this->assertSame('v0.2.2', $i->tag);
		$this->assertSame('0.2.2', $i->version);
		$this->assertSame(202, $i->versionId);
		$this->assertSame('2011-09-21', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function testTagNoDev2()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, false);
		$this->assertSame('v0.2.1', $i->tag);
		$this->assertSame('0.2.1', $i->version);
		$this->assertSame(201, $i->versionId);
		$this->assertSame('2011-09-06', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function testMoreTag()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$this->setExpectedException('Exception', 'There is more then one version tag.');
		$i = new VersionInfo($git, false);
	}

	/**
	 * @dataProvider dataProviderDevCustomTag
	 */
	public function testDevCustomTag($tag, $eTag, $eVersion, $eId)
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, true, $tag);
		$this->assertSame($eTag, $i->tag);
		$this->assertSame($eVersion, $i->version);
		$this->assertSame($eId, $i->versionId);
		$this->assertSame('2011-11-11', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175', $i->sha);
		$this->assertSame('201cf22', $i->shortSha);
	}

	public function dataProviderDevCustomTag()
	{
		return array(
			array('0.4.5-RC1', 'v0.4.5-RC1', '0.4.5-RC1', 404.9),
			array('0.4.5-RC2', 'v0.4.5-RC2', '0.4.5-RC2', 404.99),
			array('0.4.5-RC3', 'v0.4.5-RC3', '0.4.5-RC3', 404.999),
			array('v0.4.5-RC3', 'v0.4.5-RC3', '0.4.5-RC3', 404.999),
			array('0.4.5-rc3', 'v0.4.5-RC3', '0.4.5-RC3', 404.999),

			array('0.4.5-dev1', 'v0.4.5-dev1', '0.4.5-dev1', 404.5),
			array('0.4.5-dev2', 'v0.4.5-dev2', '0.4.5-dev2', 404.55),
			array('0.4.5-DEV3', 'v0.4.5-dev3', '0.4.5-dev3', 404.555),
			array('0.4.5-alfa1', 'v0.4.5-alfa1', '0.4.5-alfa1', 404.7),
			array('0.4.5-alfa2', 'v0.4.5-alfa2', '0.4.5-alfa2', 404.77),
			array('0.4.5-ALFA3', 'v0.4.5-alfa3', '0.4.5-alfa3', 404.777),
			array('0.4.5-beta1', 'v0.4.5-beta1', '0.4.5-beta1', 404.8),
			array('0.4.5-beta2', 'v0.4.5-beta2', '0.4.5-beta2', 404.88),
			array('0.4.5-BETA3', 'v0.4.5-beta3', '0.4.5-beta3', 404.888),
			array('0.4.5-FooBar3', 'v0.4.5-foobar3', '0.4.5-foobar3', -1),
		);
	}

	/**
	 * @dataProvider dataProviderDevCustomTagBad
	 */
	public function testDevCustomTagBad($tag)
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', '');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 97f7da49ab9ef150809a50ffe7cb3a24966d1cf3\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$this->setExpectedException('Exception', 'Custom tag is in invalid format (v1.2.3-RC4)');
		$i = new VersionInfo($git, true, $tag);
		dd($i);
	}

	public function dataProviderDevCustomTagBad()
	{
		return array(
			array('0.4.5RC1'),
			array('xxxx'),
			array('0.4.5-RC'),
			array('0.4.5-1'),
			array('0.4.5-RC-1'),
			array('0.4.5-RC.1'),
		);
	}

	public function testNoDevWorkingTree1()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', 'xxx');
		//$git->set('ls-files --others --exclude-standard -- Orm/', '');

		$this->setExpectedException('Exception', 'Working Tree is not clean; Clear it or add dev parametr to url: run.php?dev');
		$i = new VersionInfo($git, false);
	}

	public function testNoDevWorkingTree2()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', '');
		$git->set('ls-files --others --exclude-standard -- Orm/', 'xxx');

		$this->setExpectedException('Exception', 'Working Tree is not clean; Clear it or add dev parametr to url: run.php?dev');
		$i = new VersionInfo($git, false);
	}

	public function testDevWorkingTreeTag()
	{
		$git = new TestGit;
		$git->set('HEAD', '201cf221192137388542b4c0dedad25d38000175');
		$git->set('diff-index HEAD -- Orm/', 'xxx');
		$git->set('show -s HEAD --format="%ci"', "2011-11-11 15:12:14 +0100\n");
		$git->set('show-ref --tags', "b593bcf50f40d360aca63c783592b27c34811c42 refs/tags/v0.2.1\n" . "f1a72f448d628b803c67fdf2a07589aac71299fb refs/tags/v0.2.2\n");
		$git->set('cat-file -t b593bcf50f40d360aca63c783592b27c34811c42', "tag\n");
		$git->set('cat-file tag b593bcf50f40d360aca63c783592b27c34811c42', "object 201cf221192137388542b4c0dedad25d38000175\ntype commit\ntag v0.2.1\ntagger PetrP <prochazkapp@gmail.com> 1315342340 +0200\n\nv0.2.1\n");
		$git->set('cat-file -t f1a72f448d628b803c67fdf2a07589aac71299fb', "tag\n");
		$git->set('cat-file tag f1a72f448d628b803c67fdf2a07589aac71299fb', "object 2891122ef4c9c937189fcdfa08873b0caaecb63c\ntype commit\ntag v0.2.2\ntagger PetrP <prochazkapp@gmail.com> 1316557967 +0200\n\nv0.2.2\n");

		$i = new VersionInfo($git, true);
		$this->assertSame('v0.2.1-dev', $i->tag);
		$this->assertSame('0.2.1-dev', $i->version);
		$this->assertSame(-1, $i->versionId);
		$this->assertSame('2011-09-06', $i->date);
		$this->assertSame('201cf221192137388542b4c0dedad25d38000175 with uncommitted changes', $i->sha);
		$this->assertSame('201cf22 with uncommitted changes', $i->shortSha);
	}

}
