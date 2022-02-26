<?php
namespace app\api\controller\v1;

use app\api\model\Slide as SlideModel;

class Slide extends Auth
{
    public function index()
    {
        $Slide = new SlideModel();
        $data = $Slide->field('id,title,pic,url')->where(['status' => 1])->order('sort asc')->select();
        $this->result($data);
    }
}