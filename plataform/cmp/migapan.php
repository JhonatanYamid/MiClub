<h1>
	<a href="<?= $miga_home ?>?ver=t"><?= SIMUtil::get_traduccion('', '', 'Home', LANGSESSION); ?></a>
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		<?= $array_clubes[SIMUser::get("club")]["Nombre"] ?>
		<i class="ace-icon fa fa-angle-double-right"></i>
		<a href="<?= $script . ".php" ?>"> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?></a>
		<?php if ($_GET["action"] == "add") : ?>
			<i class="ace-icon fa fa-angle-double-right"></i> <a href="<?= $script . ".php?action=add" ?>"><?= SIMUtil::get_traduccion('', '', 'Crear', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?></a>
		<?php elseif ($_GET["action"] == "edit") : ?>
			<i class="ace-icon fa fa-angle-double-right"></i> <a href="<?= $script . ".php?action=edit&id=" . $_GET["id"]; ?>"><?= SIMUtil::get_traduccion('', '', 'Editar', LANGSESSION); ?> <?= strtoupper(SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION)); ?></a>
		<?php endif; ?>
	</small>
</h1>