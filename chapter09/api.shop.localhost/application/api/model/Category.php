<?php
namespace app\api\model;

use think\Model;

class Category extends Model
{
    public function getLeafIds($pid)
    {
        $result = [];
        $data = $this->where(['id' => $pid, 'status' => 1])->value('is_parent');
        if ($data === 0) {
            $result[] = $pid;
        } elseif ($data !== null) {
            $this->_leafIdsRecursion($pid, $result);
        }
        return $result;
    }
    private function _leafIdsRecursion($pid, &$result)
    {
        $data = $this->field('id,is_parent')->where(['parent_id' => $pid, 'status' => 1])->select();
        foreach ($data as $v) {
            if ($v['is_parent'] === 0) {
                $result[] = $v['id'];
            } else {
                $this->_leafIdsRecursion($v['id'], $result);
            }
        }
    }
}