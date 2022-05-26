<?php
header("Content-type: application/javascript");
$data = ['msg'=>'Hello World!'];
$json = json_encode($data);
echo "test({$json})";

// test({"msg":"Hello World!"})
