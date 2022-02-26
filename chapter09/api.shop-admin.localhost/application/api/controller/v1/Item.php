<?php
namespace app\api\controller\v1;

use app\api\model\Item as ItemModel;
use app\api\model\Category as CategoryModel;
use HTMLPurifier;

class Item extends Auth 
{
    public function index() 
    {
        $size = min(input('get.rows/d', 20), 100);
        $start = (input('get.page/d') - 1) * $size;
        $Item = new ItemModel();
        $data = $Item->order('id desc')->limit($start, $size)->select();
        $this->result(['total' => $Item->count(), 'rows' => $data]);
    }
    public function read($id) 
    {
        $Item = new ItemModel();
        $Category = new CategoryModel();
        $data = $Item->where('id', $id)->find();
        $data['album'] && $data['album'] = explode('|', $data['album']);
        $data['category_name'] = $Category->where('id', $data['cid'])->value('name') ?: '';
        $this->result($data);
    }
    public function save()
    {
        $data = [
            'cid' => input('post.cid/d'),
            'title' => input('post.title/s'), 
            'sell_point' => input('post.sell_point/s'),
            'price' => input('post.price/f'),
            'num' => input('post.num/d'),
            'content' => input('post.content/s', '', null),
            'status' => input('post.status/d'),
            'album' => '',
            'updated' => date('Y-m-d H:i:s')
        ];
        $data['created'] = $data['updated'];
        $data['content'] = (new HTMLPurifier())->purify($data['content']);
        $Item = new ItemModel();
        $Item->insert($data);
        $this->result(['id' => $Item->getLastInsID()]);
    }  
    public function update($id)
    {
        $data = [
            'cid' => input('put.cid/d'),
            'title' => input('put.title/s'), 
            'sell_point' => input('put.sell_point/s'),
            'price' => input('put.price/f'),
            'num' => input('put.num/d'),
            'content' => input('put.content/s', '', null),
            'status' => input('put.status/d'),
            'updated' => date('Y-m-d H:i:s')
        ];
        $data['content'] = (new HTMLPurifier())->purify($data['content']);
        $Item = new ItemModel();
        $Item->where('id', $id)->update($data);
        $this->success();
    }
    public function route()
    {
        $method = request()->method();
        if ($method === 'PATCH') {
            $this->_batchPatch();
        } elseif ($method === 'DELETE') {
            $this->_batchDelete();
        }
    }
    private function _batchPatch()
    {
        $ids = input('get.ids/s');
        $data = [];
        if (input('?patch.status')) {
            $data['status'] = input('patch.status/d');
        }
        $Item = new ItemModel();
        $Item->where('id', 'in', $ids)->update($data);
        $this->success();
    }
    private function _batchDelete()
    {
        $ids = input('get.ids/s');
        $Item = new ItemModel();
        $data = $Item->where('id', 'in', $ids)->field('pic,album')->select();
        foreach ($data as $v) {
            $file_path = $this->upload_path . $v['pic'];
            $v['pic'] && is_file($file_path) && unlink($file_path);
            foreach(explode('|', $v['album']) as $v) {
                $file_path = $this->upload_path . str_replace('[prefix]', 'album_small_', $v);
                is_file($file_path) && unlink($file_path);
                $file_path = $this->upload_path . str_replace('[prefix]', 'album_mid_', $v);
                is_file($file_path) && unlink($file_path);
                $file_path = $this->upload_path . str_replace('[prefix]', 'album_big_', $v);
                is_file($file_path) && unlink($file_path);
            }
        }
        $Item->where('id', 'in', $ids)->delete();
        $this->success();
    }
}