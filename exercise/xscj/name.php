<?php
//读取参数
    $key = $_GET['key'];
//创建连接
    $dsn="mysql:dbname=xscj;host=101.43.232.196";
    $db_user='readonly';
    $db_pass='123456';
    try{
        $pdo=new PDO($dsn,$db_user,$db_pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"));
        }catch(PDOException $e){
            echo '数据库连接失败'.$e->getMessage();
    }
//查询
    $sql="select 姓名 as name from xs where 姓名 like '$key%'" ;
    // echo $sql;
    $res=$pdo->query($sql);
    $row = json_encode($res->fetchAll(PDO::FETCH_ASSOC));
    echo $row;