<?php
namespace app\api\controller\v1;

use app\api\model\Item as ItemModel;
use app\api\model\Category as CategoryModel;

class Item extends Auth
{
    public function index()
    {
        if (input('get.view/s') === 'shopcart') {
            $this->cart();
        }
        $cid = input('get.cid/d');
        $page = max(input('get.page/d'), 1);
        $pagesize = min(input('get.pagesize/d', 20), 50);
        $sort = input('get.sort/s');
        $price_max = input('get.price_max/d', 0);
        $price_min = input('get.price_min/d', 0);
        $Item = new ItemModel();
        $Category = new CategoryModel();
        $where = ['status' => 1];
        $cids = $cid;
        if ($cid > 0) {
            $cids = $Category->getLeafIds($cid);
            $where['cid'] = ['in', $cids];
        }
        $cate = $Category->field('id,name')->where(['parent_id' => $cid, 'status' => 1])->order('sort asc')->select();
        $recommend = $Item->field('id,title,price,pic')->where($where)->order('updated desc')->limit(5)->select();
        $max_price = $Item->where($where)->max('price');
        $order = 'id desc';
        $allow_order = [
            'price-desc' => 'price desc',
            'price-asc' => 'price asc',
        ];
        if(isset($allow_order[$sort])){
            $order = $allow_order[$sort];
        }
        if ($price_min && $price_max) {
            $where['price'] = ['between', [$price_min, $price_max]];
        }
        $start = ($page - 1) * $pagesize;
        $data = $Item->field('id,title,price,pic')->where($where)->order($order)->limit($start, $pagesize)->select();
        $this->result([
            'priceMax' => $max_price,
            'item' => $data,
            'total' => $Item->where($where)->count(), 
            'category' => $cate,
            'recommend' => $recommend
        ]);
    }
    // 首页的推荐商品
    public function best()
    {
        $num = min(max(input('get.count/d', 6), 1), 50);
        $Item = new ItemModel();
        $data = $Item->field('id,title,price,pic')->where('status', 1)->order('id desc')->limit($num)->select();
        $this->result($data);
    }
    
    public function read($id)
    {
        $Item = new ItemModel();
        $data = $Item->field('id,cid,title,sell_point,price,num,album,content')->where(['id'=> $id, 'status' => 1])->find();
        $data['album'] = $data['album'] ? explode('|', $data['album']) : [];
        $data['recommend'] = $Item->field('id,title,price,pic')->where('status', 1)->order('id desc')->limit(3)->select();
        $this->result($data);
    }
    
    public function cart()
    {
        $ids = input('get.ids/s');
        $Item = new ItemModel();
        $data = $Item->field('id,title,price,num')->where(['id'=> ['in', $ids], 'status' => 1])->select();
        $this->result($data);
    }
}