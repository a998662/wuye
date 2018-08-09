<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/3/11
 * Time: 22:46
 */

namespace app\admin\controller;
use app\model\Menu as MenuModle;
use tree\Tree;


class Menu extends Init
{
    public function initialize()
    {
        parent::initialize();
        $list = MenuModle::order(array("sort" => "ASC"))->select();
        $this->list = $list;
        //树形处理
        $Tree = new Tree($list, 'id', 'parent_id');
        $list = $Tree->convert();
        $this->assign('list', $list);
    }

    //节点列表
    public function index()
    {
        $group = input('group') ?: 'admin';
        // 保存模块排序
        if (isPost()) {
            $modules = $_POST['id'];
            if ($modules) {
                $data = [];
                $k=1;
                foreach ($modules as $module) {
                    $data[] = [
                        'id'   => $module,
                        'sort' => $k++
                    ];
                }
                $MenuModel = new \app\model\Menu();
                if (false !== $MenuModel->saveAll($data)) {
                    $this->success('保存成功');
                } else {
                    $this->error('保存失败');
                }
            }
        }
        if($group != 'module-sort'){
            foreach ($this->list as $k=>$v){
                if($v['module'] == $group)
                    $list[$k] = $v;
                else
                    continue;
            }
            $max_level = $this->request->get('max', 0);

            $menus = $this->getNestMenu($list, $max_level);
            $this->assign('menus', $menus);
        }

        $this->assign('group', $group);

        return $this->fetch();
    }

    //添加节点
    public function add()
    {
        //入库操作
        if (isAjax()) {
            return $this->save();
        }
        $info = [
            'module' => input('module'),
            'parent_id' => input('parent_id'),
        ];
        $this->assign('info', $info);
        return $this->fetch('info');
    }

    //入库操作
    public function save(){
        $data = input('post.');
        if(empty($data['menus']) && !empty($data)){
            $data['status'] = $data['status'] ? 1 : 0;
            $data['url'] = strtolower($data['url']);
            $data['parent_id'] = intval($data['parent_id']);
            $data['controller'] = explode('/', $data['url'])[1];
            $data['url'] = str_replace(' ','',$data['url']);
            if($data['parent_id']){
                $parnet_ids = MenuModle::where(['id'=>$data['parent_id']])->value('parent_ids');
                $data['parent_ids'] = $parnet_ids ? $parnet_ids.$data['parent_id'].',' : $data['parent_id'].',';
            }
            if($data['id'])
                $code = MenuModle::update($data);
            else
                $code = MenuModle::insertGetId($data);
            return $code ? $this->success('操作成功',url('/menu/index/group/'.$data['module'])) : $this->error('操作失败~');
        }elseif (!empty($data['menus'])){
            $menus = $this->parseMenu($data['menus']);
            //return $menus;
            foreach ($menus as $menu) {
                MenuModle::update($menu);
            }
            return $this->success('操作成功',url('/menu/index',['group'=>$data['module']]));
        }
        return $this->success('没有要操作的节点');
    }

    //编辑节点
    public function edit()
    {
        //入库操作
        if (isAjax()) {
            return $this->save();
        }

        $info = MenuModle::find(input('id'));
        $this->assign('info', $info);
        return $this->fetch('info');
    }

    //删除节点
    public function del(){
        $table = input('table/s');
        $id = input('id/i');
        /*if(db($table)->where(['id'=>$id])->value('is_system'))
            $this->error('系统节点不可删除~');*/

        $code = db($table)->delete($id);
        if($code)
            $this->success('操作成功~');
        else
            $this->error('操作失败');
    }

    /**
     * 获取嵌套式节点
     * @param array $lists 原始节点数组
     * @param int $parent_id 父级id
     * @param int $max_level 最多返回多少层，0为不限制
     * @param int $curr_level 当前层数
     * @author 胡卫兵 <659998662@qq.com>
     * @return string
     */
    private function getNestMenu($lists = [], $max_level = 0, $parent_id = 0, $curr_level = 1)
    {
        $result = '';
        foreach ($lists as $key => $value) {
            if ($value['parent_id'] == $parent_id) {
                $disable  = $value['status'] == 0 ? 'dd-disable' : '';

                // 组合节点
                $result .= '<li class="dd-item dd3-item '.$disable.'" data-id="'.$value['id'].'">';
                $result .= '<div class="dd-handle dd3-handle">拖拽</div><div class="dd3-content"><i class="'.$value['icon'].'"></i> '.$value['name'];
                if ($value['url'] != '') {
                    $result .= '<span class="link"><i class="fa fa-link"></i> '.$value['url'].'</span>';
                }
                $result .= '<div class="action">';
                $result .= '<a href="'.url('/menu/add', ['module' => $value['module'], 'parent_id' => $value['id']]).'" data-toggle="tooltip" data-original-title="新增子节点"><i class="list-icon fa fa-plus fa-fw"></i></a><a href="'.url('/menu/edit', ['id' => $value['id']]).'" data-toggle="tooltip" data-original-title="编辑"><i class="list-icon fa fa-pencil fa-fw"></i></a>';
                if ($value['status'] == 0) {
                    // 启用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="enable" data-toggle="tooltip" data-original-title="启用"><i class="list-icon fa fa-check-circle-o fa-fw"></i></a>';
                } else {
                    // 禁用
                    $result .= '<a href="javascript:void(0);" data-ids="'.$value['id'].'" class="disable" data-toggle="tooltip" data-original-title="禁用"><i class="list-icon fa fa-ban fa-fw"></i></a>';
                }
                $result .= '<a href="'.url('/menu/del', ['id' => $value['id'], 'table' => 'menu']).'" data-toggle="tooltip" data-original-title="删除" class="ajax-get confirm"><i class="list-icon fa fa-times fa-fw"></i></a></div>';
                $result .= '</div>';

                if ($max_level == 0 || $curr_level != $max_level) {
                    unset($lists[$key]);
                    // 下级节点
                    $children = $this->getNestMenu($lists, $max_level, $value['id'], $curr_level + 1);
                    if ($children != '') {
                        $result .= '<ol class="dd-list">'.$children.'</ol>';
                    }
                }

                $result .= '</li>';
            }
        }
        return $result;
    }

    /**
     * 递归解析节点
     * @param array $menus 节点数据
     * @param int $pid 上级节点id
     * @author 胡卫兵 <659998662@qq.com>
     * @return array 解析成可以写入数据库的格式
     */
    private function parseMenu($menus = [], $pid = 0)
    {
        $sort   = 1;
        $result = [];
        foreach ($menus as $menu) {
            $result[] = [
                'id'   => (int)$menu['id'],
                'parent_id'  => (int)$pid,
                'sort' => $sort,
            ];
            if (isset($menu['children'])) {
                $result = array_merge($result, $this->parseMenu($menu['children'], $menu['id']));
            }
            $sort ++;
        }
        return $result;
    }
}