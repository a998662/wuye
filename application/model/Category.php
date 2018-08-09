<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2016/5/1 0001
 * Time: 下午 2:38
 */

namespace app\model;
use think\Model;

class Category extends Model
{
    protected $pk = 'id';

    /**
     * 递归获取父ID下的所有下级分类信息
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLists($id){
        $where = ['parent_id'=>$id];
        $where = array_merge($where);
        $res = self::field('id,name,parent_id,status')->where($where)->order('sort asc')->select()->toArray();
        if($res) {
            foreach ($res as $v) {
                $res = array_merge(self::getLists($v['id']), $res);
            }
        }
        return $res;
    }

    /**
     * 递归获取指定id下的所有id
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getIds($id){
        $where = ['parent_id'=>$id];
        $where = array_merge($where);
        $res = self::where($where)->column('id');
        if($res) {
            foreach ($res as $v) {
                $res = array_merge(self::getLists($v['id']), $res);
            }
        }
        return $res;
    }
}