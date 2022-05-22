<?php
header("Content-type: application/javsscript");
$data = ['msg'=>'Hello World!'];
$json = json_encode($data);
echo "test({$json})";
