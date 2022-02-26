<?php
namespace app\api\controller\v1;

use app\api\model\News as NewsModel;

class News extends Auth
{
    public function index()
    {
        $News = new NewsModel();
        $data = $News->field('id,title,color,status,sort,created,updated')->select();
        $this->result($data);
    }
    public function read($id)
    {
        $News = new NewsModel();
        $data = $News->field('id,title,color,url,sort')->where('id', $id)->find();
        $this->result($data);
    }
    public function save()
    {
        $data = [
            'title' => input('post.title/s'),
            'color' => input('post.color/s'),
            'sort' => input('post.sort/d'),
            'url' => input('post.url/s'),
            'status' => input('post.status/s'),
            'updated' => date('Y-m-d H:i:s')
        ];
        $data['created'] = $data['updated'];
        $News = new NewsModel();
        $News->insert($data);
        $this->result(['id' => $News->getLastInsID()]);
    }
    public function update($id)
    {
        $data = [
            'title' => input('put.title/s'),
            'color' => input('put.color/s'),
            'sort' => input('put.sort/d'),
            'url' => input('put.url/s'),
            'status' => input('put.status/s'),
            'updated' => date('Y-m-d H:i:s')
        ];
        $News = new NewsModel();
        $News->where('id', $id)->update($data);
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
        $News = new NewsModel();
        $News->where('id', 'in', $ids)->update($data);
        $this->success();
    }
    private function _batchDelete()
    {
        $ids = input('get.ids/s');
        $News = new NewsModel();
        $News->where('id', 'in', $ids)->delete();
        $this->success();
    }
}