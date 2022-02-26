<?php
namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\api\model\Item as ItemModel;

class Category extends Auth
{
    public function index()
    {
        $id = input('get.id/d', 0);
        $view = input('get.view/s', '');
        $Category = new CategoryModel();
        $data = [];
        if ($view == 'treegrid') {
            $data = $Category->field('id,name,is_parent,status,sort,created,updated')->where('parent_id', $id)->order('sort asc')->select();
            foreach ($data as &$v) {
                $v['state'] = $v['is_parent'] === 1 ? 'closed' : 'open';
                unset($v['is_parent']);
            }
        } elseif ($view == 'tree') {
            $data = $Category->field('id,name,is_parent')->where(['parent_id' => $id, 'status' => 1])->order('sort asc')->select();
            foreach ($data as &$v) {
                $v['text'] = $v['name'];
                unset($v['name']);
                $v['state'] = $v['is_parent'] === 1 ? 'closed' : 'open';
                unset($v['is_parent']);
            }
        }
        unset($v);
        $this->result($data);
    }
    public function save()
    {
        $Category = new CategoryModel();
        $parent_id = input('post.parent_id/d', 0);
        $data = [
            'parent_id' => $parent_id,
            'name' => input('post.name/s'),
            'status' => input('post.status/d'),
            'sort' => input('post.sort/d'),
            'is_parent' => 0,
            'updated' => date('Y-m-d H:i:s')
        ];
        $data['created'] = $data['updated'];
        if ($Category->insert($data)) {
            $id = $Category->getLastInsID();
            $Category->where(['id' => $parent_id, 'is_parent' => 0])->update(['is_parent' => 1]);
            $this->success(['id' => $id]);
        } else {
            $this->error();
        }
    }
    public function update($id)
    {
        $Category = new CategoryModel();
        $Category->where(['id' => $id])->update([
            'name' => input('put.name/s'),
            'sort' => input('put.sort/d'),
            'status' => input('put.status/d'),
            'updated' => date('Y-m-d H:i:s')
        ]);
        $this->success();
    }
    public function delete($id)
    {
        $Category = new CategoryModel();
        if ($Category->where(['parent_id' => $id])->value('id')) {
            $this->error();
        }
        $parent_id = $Category->where(['id' => $id])->value('parent_id');
        $Category->where(['id' => $id])->delete();
        if (!$Category->where(['parent_id' => $parent_id])->value('id')) {
            $Category->where(['id' => $parent_id])->update(['is_parent' => 0]);
        }
        $Item = new ItemModel();
        $Item->where(['cid' => $id])->update(['cid' => 0]);
        $this->success();
    }
}
