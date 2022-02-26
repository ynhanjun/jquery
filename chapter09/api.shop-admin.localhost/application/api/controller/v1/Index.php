<?php
namespace app\api\controller\v1;

class Index extends Auth
{
    public function index()
    {
        $this->success('ok');
    }
}