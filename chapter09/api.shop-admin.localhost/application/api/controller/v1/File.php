<?php
namespace app\api\controller\v1;

use think\Image;
use app\api\model\Item as ItemModel;
use app\api\model\Slide as SlideModel;

class File extends Auth
{
    private $name = '';
    private $type = '';
    public function route()
    {
        $method = request()->method();
        if ($method === 'POST') {
            $this->save();
        } elseif ($method === 'DELETE') {
            $this->delete();
        }
    }
    public function save()
    {
        $this->name = input('get.name/s');
        $this->type = input('get.type/s', 'image');
        $ext_arr = [
            'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
            // 'flash' => ['swf', 'flv'],
            // 'media' => ['swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
            // 'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
        ];
        $max_size = 1000000;
        $callback = false;
        if (in_array($this->name, ['pic', 'album', 'hot'])) {
            $callback = '_thumb';
        }
        $path = $this->_upload($ext_arr, $max_size, $callback);
        $relation_id = $this->_relationUpload($path);
        $this->result(['path' => $path, 'relation_id' => $relation_id]);
    }
    public function delete()
    {
        $this->name = input('delete.name/s');
        $path = input('delete.path/s');
        $this->_relationDelete($path);
        if ($this->name === 'pic' || $this->name === 'hot') {
            $file_path = $this->upload_path . $path;
            is_file($file_path) && unlink($file_path);
        } elseif ($this->name === 'album') {
            $file_path = $this->upload_path . str_replace('[prefix]', 'album_small_', $path);
            is_file($file_path) && unlink($file_path);
            $file_path = $this->upload_path . str_replace('[prefix]', 'album_mid_', $path);
            is_file($file_path) && unlink($file_path);
            $file_path = $this->upload_path . str_replace('[prefix]', 'album_big_', $path);
            is_file($file_path) && unlink($file_path);
        }
        $this->success();
    }
    private function _upload($ext_arr, $max_size, $callback = false)
    {
        $name = $this->name;
        $dir_name = $this->type;
        $save_path = $this->upload_path;
        if (!empty($_FILES[$name]['error'])) {
            switch ($_FILES[$name]['error']) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $this->error('upload', $error);
        }
        if (empty($_FILES)) {
            $this->error('upload', '请选择文件。');
        }
        $file_name = $_FILES[$name]['name'];
        $tmp_name = $_FILES[$name]['tmp_name'];
        $file_size = $_FILES[$name]['size'];
        if (!$file_name) {
            $this->error('upload', '请选择文件。');
        }
        if (is_dir($save_path) === false) {
            $this->error('upload', '上传目录不存在。');
        }
        if (is_writable($save_path) === false) {
            $this->error('upload', '上传目录没有写权限。');
        }
        if (is_uploaded_file($tmp_name) === false) {
            $this->error('upload', '上传失败。');
        }
        if ($file_size > $max_size) {
            $this->error('upload', '上传文件大小超过限制。');
        }
        if (empty($ext_arr[$dir_name])) {
            $this->error('upload', '目录名不正确。');
        }
        $temp_arr = explode('.', $file_name);
        $file_ext = strtolower(trim(array_pop($temp_arr)));
        if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
            $this->error('upload', '上传文件扩展名是不允许的扩展名。只允许' . implode(',', $ext_arr[$dir_name]) . '格式。');
        }
        $sub_path = date('Y-m/d/');
        if ($dir_name !== '') {
            $sub_path = "$dir_name/$sub_path";
        }
        $save_path .= $sub_path;
        file_exists($save_path) || mkdir($save_path, 0755, true);
        $new_file_name = md5(microtime(true)) . '.' . $file_ext;
        $file_path = $save_path . $new_file_name;
        if ($callback) {
            return $this->$callback($tmp_name, $save_path, $sub_path, $new_file_name);
        }
        if (move_uploaded_file($tmp_name, $file_path) === false) {
            $this->error('upload', '上传文件失败。');
        }
        chmod($file_path, 0644);
        return $sub_path . $new_file_name;
    }
    private function _thumb($tmp_name, $save_path, $ymd_path, $new_file_name)
    {
        $batch = [];
        if ($this->name === 'pic') {
            $batch['pic'] = [220, 220];
            $prefix = 'pic_';
        } elseif ($this->name === 'album') {
            $batch['album_small'] = [100, 100];
            $batch['album_mid'] = [350, 350];
            $batch['album_big'] = [800, 800];
            $prefix = '[prefix]';
        } elseif ($this->name === 'hot') {
            $batch['hot'] = [670, 240];
            $prefix = 'hot_';
        }
        foreach($batch as $k => $v){
            $file_path = $save_path . $k . '_' . $new_file_name;
            Image::open($tmp_name)->thumb($v[0], $v[1], Image::THUMB_FILLED)->save($file_path);
            chmod($file_path, 0644);
        }
        return $ymd_path . $prefix . $new_file_name;
    }
    private function _relationUpload($path)
    {
        $id = input('get.relation_id/d');
        $relation = strtolower(input('get.relation/s'));
        if ($relation === 'item') {
            return $this->_relationUploadItem($id, $path);
        } elseif ($relation === 'slide') {
            return $this->_relationUploadSlide($id, $path);
        }
        return $id;
    }
    private function _relationDelete($path)
    {
        $id = input('get.relation_id/d');
        $relation = strtolower(input('get.relation/s'));
        if ($relation === 'item') {
            return $this->_relationDeleteItem($id, $path);
        } elseif ($relation === 'slide') {
            return $this->_relationDeleteSlide($id, $path);
        }
        return false;
    }
    private function _relationUploadItem($id, $path)
    {
        $data = ['updated' => date('Y-m-d H:i:s')];
        if ($this->name === 'pic') {
            $data['pic'] = $path;
        }
        $Item = new ItemModel();
        if ($id) {
            if ($this->name === 'album') {
                $album = $Item->where('id', $id)->value('album');
                $data['album'] = $album ? "$album|$path" : $path;
            }
            $Item->where('id', $id)->update($data);
            return $id;
        }
        $data['album'] = $this->name === 'album' ? $path : '';
        $data['content'] = '';
        $data['created'] = $data['updated'];
        $data['status'] = 2;
        $data['title'] = '无标题';
        $Item->insert($data);
        return $Item->getLastInsID();
    }
    private function _relationDeleteItem($id, $path)
    {
        $Item = new ItemModel();
        $data = [];
        if ($this->name === 'pic') {
            $data['pic'] = '';
        } elseif ($this->name === 'album') {
            $album = explode('|', $Item->where('id', $id)->value('album'));
            foreach ($album as $k => $v) {
                if ($v === $path) {
                    unset($album[$k]);
                    break;
                }
            }
            $data['album'] = implode('|', $album);
        }
        return $Item->where('id', $id)->update($data);
    }
    private function _relationUploadSlide($id, $path)
    {
        $data = ['updated' => date('Y-m-d H:i:s')];
        if ($this->name === 'hot') {
            $data['pic'] = $path;
        }
        $Slide = new SlideModel();
        if ($id) {
            $Slide->where('id', $id)->update($data);
            return $id;
        }
        $data['created'] = $data['updated'];
        $data['status'] = 2;
        $data['title'] = '无标题';
        $Slide->insert($data);
        return $Slide->getLastInsID();
    }
    private function _relationDeleteSlide($id, $path)
    {
        $Slide = new SlideModel();
        $data = [];
        if ($this->name === 'hot') {
            $data['pic'] = '';
        }
        return $Slide->where('id', $id)->update($data);
    }
}