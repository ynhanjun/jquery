<?php
namespace app\index\controller;

use app\api\controller\v1\Auth;
use think\Db;
use Exception;

class Index extends Auth
{
    public function __construct()
    {
        $this->_cors();
    }
    public function index()
    {
        try{
          $data = Db::query('SHOW TABLES');
          if (empty($data)) {
              throw new Exception('数据库中没有数据表。');
          }
        } catch (Exception $e) {
            $this->success('前台API：数据库测试失败，错误信息：' . $e->getMessage());
        }
        $this->success('前台API：数据库测试成功。');
    }
}
