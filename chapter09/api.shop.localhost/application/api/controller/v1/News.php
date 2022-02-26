<?php
namespace app\api\controller\v1;

use app\api\model\News as NewsModel;

class News extends Auth
{
    public function index()
    {
        $News = new NewsModel();
        $data = $News->field('id,title,color,url')->where(['status' => 1])->order('sort asc')->select();
        $this->result($data);
    }
}