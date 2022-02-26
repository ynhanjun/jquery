<?php
namespace app\api\controller\v1;

use app\api\model\Slide as SlideModel;

class Slide extends Auth
{
    public function index()
    {
        $Slide = new SlideModel();
        $data = $Slide->field('id,title,status,sort,created,updated')->select();
        $this->result($data);
    }
    public function read($id)
    {
        $Slide = new SlideModel();
        $data = $Slide->field('id,title,pic,url,sort')->where('id', $id)->find();
        $this->result($data);
    }
    public function save()
    {
        $data = [
            'title' => input('post.title/s'),
            'sort' => input('post.sort/d'),
            'url' => input('post.url/s'),
            'status' => input('post.status/s'),
            'updated' => date('Y-m-d H:i:s')
        ];
        $data['created'] = $data['updated'];
        $Slide = new SlideModel();
        $Slide->insert($data);
        $this->result(['id' => $Slide->getLastInsID()]);
    }
    public function update($id)
    {
        $data = [
            'title' => input('put.title/s'),
            'sort' => input('put.sort/d'),
            'url' => input('put.url/s'),
            'status' => input('put.status/s'),
            'updated' => date('Y-m-d H:i:s')
        ];
        $Slide = new SlideModel();
        $Slide->where('id', $id)->update($data);
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
        $Slide = new SlideModel();
        $Slide->where('id', 'in', $ids)->update($data);
        $this->success();
    }
    private function _batchDelete()
    {
        $ids = input('get.ids/s');
        $Slide = new SlideModel();
        $data = $Slide->where('id', 'in', $ids)->field('pic')->select();
        foreach ($data as $v) {
            $file_path = $this->upload_path . $v['pic'];
            $v['pic'] && is_file($file_path) && unlink($file_path);
        }
        $Slide->where('id', 'in', $ids)->delete();
        $this->success();
    }
}