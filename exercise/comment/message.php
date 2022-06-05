<?php  
	// 从comment.json文件中读取数据到PHP变量    
	$json_string = file_get_contents('data/comment.json');          
	// 把JSON字符串强制转成PHP数组    
	$data = json_decode($json_string, true);    
	// 在数组中插入新数据
	array_push($data,$_POST);
	// 把处理后的数据写入comment.json
	$json_new = json_encode($data);
	file_put_contents('data/comment.json', $json_new);
?>