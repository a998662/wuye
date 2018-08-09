<?php


namespace app\common\behavior;

/**
 * 注册钩子
 * @package app\common\behavior
 * @author 胡卫兵 <659998662@qq.com>
 */
class Hook
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params  行为参数
     * @return void
     */
    public function run($params)
    {
        // 获取当前模块名称
        $module = '';
        $dispatch = request()->dispatch();
        if (isset($dispatch['module'])) {
            $module = $dispatch['module'][0];
        }

        if (strpos('--'.$_SERVER['HTTP_HOST'],'admin') && $module != 'admin') {
            // 修改默认访问控制器层
            config('url_controller_layer', 'admin');
            // 修改视图模板路径
            config('template.view_path', APP_PATH. $module. '/view/admin/');
        }
    }
}
