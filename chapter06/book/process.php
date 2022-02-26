<?php
$lock = new FileLock();
$lock->lock();
$filepath = 'data/data.json';
$json = file_get_contents($filepath);
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === '') {
    exit ($json);
}
$data = json_decode($json, true);
if ($action === 'add') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    checkIdUnique($id, $data);
    $data[] = $_POST;
} elseif ($action === 'update') {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $newId = isset($_POST['id']) ? $_POST['id'] : '';
    if ($id !== $newId) {
        checkIdUnique($newId, $data);
    }
    foreach ($data as $k => $v) {
        if ($v['id'] === $id) {
            $data[$k] = $_POST;
            break;
        }
    }
} elseif ($action === 'del') {
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    foreach ($data as $k => $v) {
        if ($v['id'] === $id) {
            unset($data[$k]);
        }
    }
}
file_put_contents($filepath, json_encode($data));
$lock->unlock();

function checkIdUnique($id, $data) {
    foreach ($data as $v) {
        if ($v['id'] === $id) {
            header('HTTP/1.1 403 Forbidden');
            exit ('图书编号已经存在！');
        }
    }
}

class FileLock
{
    private $fp;
    private $file = 'data/lock.txt';
    public function lock()
    {
        $this->fp = fopen($this->file, 'w');
        if (!flock($this->fp, LOCK_EX)) {
            exit ('文件加锁失败。');
        }
    }
    public function unlock()
    {
        flock($this->fp, LOCK_UN);
    }
}