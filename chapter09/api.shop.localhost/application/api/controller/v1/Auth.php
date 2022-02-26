<?php

namespace app\api\controller\v1;

use think\exception\HttpResponseException;

class Auth
{
    protected $header = [];
    public function __construct()
    {
        $this->_cors();
    }
    protected function _cors()
    {
        $this->header['Access-Control-Allow-Origin'] = '*';
    }
    protected function success($data = '', $code = 200)
    {
        $this->result($data, 'json', $code);
    }
    protected function error($message = '', $code = 403)
    {
        $this->result($message, '', $code);
    }
    protected function result($data, $type = 'json', $code = 200)
    {
        throw new HttpResponseException(response($data, $code, $this->header, $type));
    }
}
