<?php
namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;

class Category extends Auth
{
    public function index()
    {
        $id = input('get.id/d', 0);
        $view = input('get.view/s', '');
        $Category = new CategoryModel();
        $data = [];
        if ($view === 'nav') {
            $data = $Category->field('id,parent_id,name')->where(['status' => 1])->order('sort asc')->select();
        } elseif ($view === 'crumbs') {
            $find = function($id) use($Category) {
                return $Category->field('id,parent_id,name')->where(['id' => $id, 'status' => 1])->find();
            };
            $rst = $find($id);
            if ($rst) {
                $data[] = $rst;
                $rst = $find($rst['parent_id']);
                if ($rst) {
                    $data[] = $rst;
                    $rst = $find($rst['parent_id']);
                    $rst && $data[] = $rst;
                }
            }
            $data = array_reverse($data);
        }
        $this->result($data);
    }
}
