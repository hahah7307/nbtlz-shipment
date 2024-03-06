<?php

namespace app\Manage\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;

class CategoryModel extends Model
{
    const STATE_ACTIVE = 1;

    protected $name = 'category';

    protected $resultSetType = 'collection';

    public function parent(): \think\model\relation\HasOne
    {
        return $this->hasOne('CategoryModel', 'id', 'parent_id');
    }

    // 无限递归+排序

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    static public function category_format($state = 0, $list = [], $pid = 0, $level = 1)
    {
        return self::list_sort($state, $list, $pid, $level);
    }

    /**
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    protected function list_sort($state, $list = [], $pid = 0, $level = 1)
    {
        $category = self::with([])->where(['state' => ['egt', $state], 'parent_id' => $pid])->order('sort asc')->select()->toArray();
        if ($category) {
            foreach ($category as $item) {
                $item['level'] = $level;
                $item['category_name'] = str_repeat('&nbsp;', ($level -1) * 6 + 1) . "↳" . $item['name'];
                $list[] = $item;
                $list = self::list_sort($state, $list, $item['id'], $level + 1);
            }
        }
        return $list;
    }
}
