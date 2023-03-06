<?php

$file = file_get_contents($_GET['foto']);

$encoded = base64_encode($file);

echo $encoded;

?>