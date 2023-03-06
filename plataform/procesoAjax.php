<?
print_r($_POST['base64']);
    if(!empty($_POST['base64']))
    {
        $img = $_POST['base64'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $fileData = base64_decode($img);
        $fileName = uniqid().$_GET["id"].'.png';
        $frm["Imagen"] = $fileName;
        file_put_contents($fileName, $fileData);
    }
?>


<?php
print_r($_POST);
echo "<br>Estás usando Ajax <br> ".$_POST["Nombre"];
echo "<br>Base64".$_POST['base64'];
?>
