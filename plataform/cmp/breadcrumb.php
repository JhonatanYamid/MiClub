    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="<?=$miga_home?>?ver=t">Home</a>
        </li>
        <li ><?=$array_clubes[ SIMUser::get("club") ]["Nombre"] ?></li>
        <li class="active" ><a href="<?=$script.".php"?>"><?php echo SIMReg::get( "title" )?></a></li>
        <?php if ($_GET["action"]=="add"): ?>
        <li><a href="<?=$script.".php?action=add"?>">Crear <?php echo SIMReg::get( "title" )?></a></li>
        <?php elseif($_GET["action"]=="edit"): ?>
        <li><a href="<?=$script.".php?action=edit&id=".$_GET["id"];?>">Editar <?php echo SIMReg::get( "title" )?></a></li>
        <?php	endif; ?>
    </ul><!-- /.breadcrumb -->
