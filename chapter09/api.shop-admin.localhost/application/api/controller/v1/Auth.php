<?php

namespace app\api\controller\v1;

use think\exception\HttpResponseException;
use Firebase\JWT\JWT;

class Auth
{
    protected $header = [];
    private $secret_key = 'your-top-secrect-key';
    private $iss = 'auth';
    private $user = [
        'id' => 1,
        'username' => 'admin',
        'password' => '123456'
    ];
    protected $upload_path = '../../www.shop.localhost/upload/';
    public function __construct()
    {
        $this->_cors();
        if (request()->action() !== 'token') {
            $this->_checkToken();
        }
    }
    public function token()
    {
        $input = [
            'username' => input('post.username/s'),
            'password' => input('post.password/s')
        ];
        if (!($input['username'] === $this->user['username'] && $input['password'] === $this->user['password'])) {
            $this->error('login', '用户名或密码错误。');
        }
        $issuedAt = time();
        $token = [
            'iss' => $this->iss, // 该JWT的签发者
            'iat' => $issuedAt,  // 在什么时候签发的token
            'nbf' => $issuedAt,  // token在此时间之前不能被接收处理 notBefore 
            'exp' => $issuedAt + (60 * 60 * 24 * 7), // token什么时候过期 expire 7天后过期
            'data' => ['user' => ['id' => $this->user['id']]]
        ];
        $this->success([
            'token' => JWT::encode($token, $this->secret_key),
            'username' => $this->user['username'],
        ]);
    }
    protected function _checkToken()
    {
        $auth = request()->header('Authorization');
        if (!$auth) {
            $this->error('auth', '未收到Authorization请求头');
        }
        list($token) = sscanf($auth, 'Bearer %s');
        if (!$token) {
            $this->error('auth', 'Authorization请求头格式有误');
        }
        try {
            $rst = JWT::decode($token, $this->secret_key, ['HS256']);
            if ($rst->iss != $this->iss) {
                $this->error('auth', 'iss不匹配');
            }
            if (!isset($rst->data->user->id)) {
                $this->error('auth', '用户id不存在');
            }
        } catch (\Exception $e) {
            $this->error('auth', 'token认证失败：' . $e->getMessage());
        }
    }
    protected function _cors()
    {
        $this->header['Access-Control-Allow-Origin'] = '*';
    }
    protected function success($data = '', $code = 200)
    {
        $this->result($data, 'json', $code);
    }
    protected function error($error = '', $msg = '', $code = 403)
    {
        $this->result(['error' => $error, 'msg' => $msg], 'json', $code);
    }
    protected function result($data, $type = 'json', $code = 200)
    {
        throw new HttpResponseException(response($data, $code, $this->header, $type));
    }
}
