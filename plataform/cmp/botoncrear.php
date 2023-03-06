<div class="ace-settings-container" id="ace-settings-container">
    <button class="btn btn-danger btnRedirect" rel="<?php echo $script ?>.php?action=add">
        <i class="ace-icon fa fa-file align-top bigger-125"></i>
        <?= SIMUtil::get_traduccion('', '', 'Nuevo', LANGSESSION); ?> <?= SIMUtil::get_traduccion('', '', SIMReg::get("title"), LANGSESSION); ?>
    </button>
</div>