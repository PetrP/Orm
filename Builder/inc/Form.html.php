<!DOCTYPE HTML> <meta charset="utf-8">

<title>Orm\Builder</title>
<?php
	function e($s)
	{
		return htmlspecialchars($s, ENT_QUOTES);
	}
?>
<style>
	fieldset {
		float: left;
		margin: 70px 70px 0px 20px;
	}
</style>

<h1><?=e($versions->getHeadShortSha())?><?php if (!$versions->isCleanWorkingTree()): ?> with uncommitted changes<?php endif; ?></h1>
<pre><?=e($versions->getHeadInfo())?></pre>
<fieldset>
	<legend>Quick dev build</legend>
		<form method="GET">
			<button>run</button>
			<input type="hidden" name="mode" value="<?=e(Orm\Builder\Runner::QUICK)?>">
		</form>
</fieldset>

<fieldset>
	<legend>Dev build with tests</legend>
		<form method="GET">
			<button>run</button>
			<input type="hidden" name="mode" value="<?=e(Orm\Builder\Runner::TEST)?>">
		</form>
</fieldset>

<fieldset class="developmentBuild">
	<legend>Development build</legend>
		<?php $default = $versions->getCurrentMajor() ?>
		<select class="major" <?php if ($default): ?>disabled<?php endif; ?>>
			<option value=""></option>
			<?php foreach ($versions->getMajorVersions() as $version): ?>
				<option <?php if ($default === $version): ?>selected<?php endif; ?> value="<?=e($version)?>"><?=e($version)?></option>
			<?php endforeach; ?>
		</select>
		<?php foreach ($versions->getDevelopmentStageVersions() as $stage): ?>
			<label><input class="stage" name="stage" type="radio" value="<?=e($stage)?>"> <?=e($stage)?></label>
		<?php endforeach; ?>
		<input class="stageNumber" size="2" type="text" placeholder="1">
		<br>
		<form method="GET">
			<input name="version" type="text" required>
			<button>run</button>
			<input type="hidden" name="mode" value="<?=e(Orm\Builder\Runner::DEVELOPMENT)?>">
		</form>
</fieldset>

<fieldset class="stableBuild">
	<legend>Stable build</legend>
		<form method="GET">
			<?php $default = $versions->getCurrentTag() ?>
			<select class="version" name="version" required>
				<option value=""></option>
				<?php foreach ($versions->getStableVersions() as $tmp): list($version, $hash) = $tmp; ?>
					<option <?php if ($default === $version): ?>selected<?php endif; ?> value="<?=e($version)?>" data-hash="<?=e($hash)?>"><?=e($version)?></option>
				<?php endforeach; ?>
			</select>
			<p>
				<div class="info" style="display: none;">
					Please move HEAD on <b class="hash"></b>.
					<pre>git checkout -b build_<span class="hash"></span> <span class="hash"></span></pre>
				</div>
			</p>
			<button>run</button>
			<input type="hidden" name="mode" value="<?=e(Orm\Builder\Runner::STABLE)?>">
		</form>
</fieldset>

<fieldset>
	<legend>Create new stable build</legend>
		<form method="GET">
			<?php $default = $versions->getCurrentMajor() ?>
			<?php if ($default AND $versions->getCurrentTag()) $default = NULL;  ?>
			<select class="version" name="version" required>
				<option value=""></option>
				<?php foreach ($versions->getNextMinorVersions() as $major => $version): ?>
					<option <?php if ($default === $major): ?>selected<?php endif; ?> value="<?=e($version)?>"><?=e($version)?></option>
				<?php endforeach; ?>
			</select>
			<button>run</button>
			<input type="hidden" name="mode" value="<?=e(Orm\Builder\Runner::NEW_STABLE)?>">
		</form>
</fieldset>


<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script>
	(function () {

		var build = $('fieldset.developmentBuild');
		var version = build.find('input[name="version"]');
		var major = build.find('select.major');
		var stage = build.find('input.stage');
		var stageNumber = build.find('input.stageNumber');

		$(major).add(stage).add(stageNumber).on('change keyup click', function () {
			version.val((major.val() || 'v0.0') + '.0-' + (stage.filter(':checked').val() || 'dev') + (stageNumber.val() || '1'));
		}).eq(0).change();

		var stages = <?=json_encode(implode('|', array_map('preg_quote', $versions->getDevelopmentStageVersions())))?>+'';
		var regexp = new RegExp('^(v[0-9]+\.[0-9]+)\.0-(' + stages + ')([0-9]+)$');
		version.on('change keyup', function () {
			major.removeAttr('disabled');
			var match;
			if (match = this.value.match(regexp))
			{
				var mv = match[1] || 'v0.0';
				var sv = match[2] || 'dev';
				var snv = match[3] || '1';
				major.val(mv);
				stage.prop('checked', false).filter('[value="' + sv + '"]').prop('checked', true);
				stageNumber.val(snv);
				this.setCustomValidity('');
			}
			else
			{
				this.setCustomValidity('Invalid version format.');
			}
		}).focusout(function () {
			if (!this.value)
			{
				$(major).change();
				this.setCustomValidity('');
			}
		});

	})();

	(function () {

		var build = $('fieldset.stableBuild');
		var version = build.find('select.version');
		var info = build.find('.info');

		version.on('change', function () {
			var hash = (version.find('option:selected').data('hash') || '').substr(0, 7);
			var currentHash = <?=json_encode($versions->getHeadShortSha())?>+'';
			info.find('.hash').text(hash);
			if (!hash || hash === currentHash.substr(0, 7))
			{
				info.hide();
				this.setCustomValidity('');
			}
			else
			{
				info.show();
				this.setCustomValidity(info.text());
			}
		}).change();

	})();

</script>
