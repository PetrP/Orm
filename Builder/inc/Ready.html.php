<!DOCTYPE HTML> <meta charset="utf-8">

<?php
	function e($s)
	{
		return htmlspecialchars($s, ENT_QUOTES);
	}
?>

<h1>OK</h1>
<a href="run.php">go back</a>

<h2><?=e($info->tag)?></h2>

<h3><?=e($versions->getHeadShortSha())?><?php if (!$versions->isCleanWorkingTree()): ?> with uncommitted changes<?php endif; ?></h3>
<pre><?=e($versions->getHeadInfo())?></pre>

<?php if ($mode === Orm\Builder\Runner::TEST): ?>
	<h3>Tests</h3>
	<ul>
		<li><a target="_blank" href="php53/Nette_with_namespaces/tests">/php53/Nette_with_namespaces/tests</a></li>
		<li><a target="_blank" href="php53/Nette_without_namespaces/tests">/php53/Nette_without_namespaces/tests</a></li>
	</ul>
	<ul>
		<li><a target="_blank" href="php52/Nette_with_namespaces/tests">/php52/Nette_with_namespaces/tests</a></li>
		<li><a target="_blank" href="php52/Nette_without_namespaces/tests">/php52/Nette_without_namespaces/tests</a></li>
		<li><a target="_blank" href="php52/Nette_without_namespaces_partial/tests">/php52/Nette_without_namespaces_partial/tests</a></li>
	</ul>
<?php endif; ?>

<?php if ($mode === Orm\Builder\Runner::DEVELOPMENT OR $mode === Orm\Builder\Runner::STABLE OR $mode === Orm\Builder\Runner::NEW_STABLE): ?>
	<h3>Download</h3>
	<ul>
		<li><a href="ftp/download/Orm-<?=e($info->tag)?>.zip">Orm-<?=e($info->tag)?>.zip</a></li>
	</ul>
<?php endif; ?>

<?php if ($mode === Orm\Builder\Runner::DEVELOPMENT OR $mode === Orm\Builder\Runner::STABLE OR $mode === Orm\Builder\Runner::NEW_STABLE): ?>
	<h3>Composer</h3>
	<ul>
		<li><a href="ftp/composer/Orm-<?=e($info->tag)?>.zip">download archive</a></li>
	</ul>
<?php endif; ?>

<?php if ($mode === Orm\Builder\Runner::DEVELOPMENT OR $mode === Orm\Builder\Runner::STABLE OR $mode === Orm\Builder\Runner::NEW_STABLE): ?>
	<h3>API</h3>
	<ul>
		<li><a target="_blank" href="ftp/api/<?=e($info->tag)?>/">html</a></li>
		<li><a href="ftp/download/Orm-<?=e($info->tag)?>.zip">download archive</a></li>
	</ul>
<?php endif; ?>

<?php if ($mode === Orm\Builder\Runner::QUICK OR $mode === Orm\Builder\Runner::TEST OR $mode === Orm\Builder\Runner::DEVELOPMENT OR $mode === Orm\Builder\Runner::STABLE OR $mode === Orm\Builder\Runner::NEW_STABLE): ?>
	<h3>Source files</h3>
	<ul>
		<li><a target="_blank" href="php53/Orm">/php53/Orm</a></li>
		<li><a target="_blank" href="php52/Orm">/php52/Orm</a></li>
	</ul>
<?php endif; ?>
