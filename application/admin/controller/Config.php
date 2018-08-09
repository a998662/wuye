<?php
/**
 * Created by PhpStorm.
 * User: 胡卫兵 <659998662@qq.com>
 * Date: 2017/9/23
 * Time: 13:04
 */

namespace app\admin\controller;
use app\model\Module as ModuleModel;
use app\model\Config as ConfigModel;

class Config extends Init
{
    public function index()
    {
        $group = input('group') ?: 'base';
        // 保存数据
        if (isPost()) {
            $data = $this->request->post();

            if (!ModuleModel::where(['name'=>$group])->value('id')) {
                $Config = ConfigModel::where(['group'=>$group])->column('name');
                 !is_array($data['clear_cache_type']) ?: $data['clear_cache_type'] = implode(',', $data['clear_cache_type']);

                foreach ($data as $name => $value) {
                    if(in_array($name,$Config))
                        ConfigModel::where(['name'=>$name])->update(['value' => $value, 'group'=>$group]);
                    else
                        ConfigModel::insertGetId(['value' => $value,'name'=>$name, 'group'=>$group]);
                }

            } else {
				foreach ($data as $name => $value) {
					$Config = ConfigModel::where('group',$group)->column('name');
					if(in_array($name,$Config))
						ConfigModel::where('name',$name)->update(['value' => $value, 'group'=>$group]);
					else
						ConfigModel::insertGetId(['value' => $value,'name'=>$name, 'group'=>$group]);
				}
                // 保存模块配置
                if (false === ModuleModel::where('name', $group)->update(['config' => json_encode($data)])) {
                    $this->error('更新失败');
                }
                // 非开发模式，缓存数据
                if (config('develop_mode') == 0) {
                    cache('module_config_'.$group, $data);
                }
            }
            cache('system_config', null);
            // 记录行为
            //action_log('system_config_update', 'admin_config', 0, UID, "分组($group)");
            $this->success('更新成功', url('/config/index', ['group' => $group]));
        }
        if($group == 'system'){
            $module = ModuleModel::field('title,name')->select();
            $this->assign('module',$module);
        }

        $this->assign('group',$group);
        return $this->fetch();
    }
}