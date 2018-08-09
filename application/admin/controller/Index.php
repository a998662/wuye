<?php

namespace app\admin\controller;


use app\model\Users;
use think\Container;
use think\facade\Cache;
use app\model\Category;
use app\model\Video;
use think\helper\Time;

class Index extends Init {
    //后台首页
    public function index() {
        $category = Category::where('parent_id', 0)->select()->toArray();
        foreach ($category as $v) {
            $count[$v['id']]          = $v;
            $count[$v['id']]['total'] = Video::where('channel', $v['alias'])->count();
        }

        //select DATE_FORMAT(create_time,'%Y%m%d') days,count(caseid) count from tc_case group by days;
        //echo htmlspecialchars('<html></html>');
        //return;

        // 上周开始和结束的时间戳
        list($start, $end) = Time::lastWeek();
        $lastWeekDay = $this->get_week($start)['date'];
        $lastWeekCount = Users::query("select DATE_FORMAT(FROM_UNIXTIME(insert_time),'%Y%m%d') days,count(id) count from 360_users where insert_time > ".$start." and insert_time < ".$end." group by days");

        foreach ($lastWeekCount as $v){
            $temp1[$v['days']] = $v['count'];
        }
        foreach ($lastWeekDay as $v){
            $lastWeek[] = $temp1[$v] ?: 0;
        }

        // 本周开始和结束的时间戳
        list($start, $end) = Time::week();

        $thisWeekDay = $this->get_week($start)['date'];
        $thisWeekCount = Users::query("select DATE_FORMAT(FROM_UNIXTIME(insert_time),'%Y%m%d') days,count(id) count from 360_users where insert_time > ".$start." group by days");
        foreach ($thisWeekCount as $v){
            $temp[$v['days']] = $v['count'];
        }

        foreach ($thisWeekDay as $k=>$v){
            $thisWeek[] = $temp[$v] ?: 0;
        }

        $this->assign(['thisWeek'=>json_encode($thisWeek),'lastWeek'=>json_encode($lastWeek)]);
        $this->assign('count', $count);
        return $this->fetch();
    }
    function get_week($time, $format = "Ymd") {
        $week = date('w',$time);
        $weekname=array('星期一','星期二','星期三','星期四','星期五','星期六','星期日');
        //星期日排到末位
        if(empty($week)){
            $week=7;
        }
        for ($i=0;$i<=6;$i++){
            $data['date'][$i] = date($format,strtotime( '+'. $i+1-$week .' days',$time));
            $data['week'][$i] = $weekname[$i];
        }
        return $data;
    }

    //个人设置
    public function profile() {
        if (input('ajax')) {
            $data                = input('post.');
            $data['code']        = getRandCode(5);
            $data['pwd_view']    = $data['password'];
            $data['status']      = $data['status'] == 'on' ? 1 : 0;
            $data['password']    = md5($data['code'] . $data['password']);
            $data['insert_time'] = time();
            $data['update_time'] = time();

            unset($data['ajax']);

            $res                 = Users::update($data);
            if ($res) {
                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        }
        $info = Users::find(input('id'));
        $role = \app\model\Role::where(['status' => 1])->select();
        $this->assign('info', $info);
        $this->assign('role', $role);
        return $this->fetch();
    }

    //清除缓存
    function clearCache() {
        if (!empty(config('clear_cache_type'))) {
            $app         = Container::get('app');
            $runtimePath = $app->getRuntimePath();
            foreach (config('clear_cache_type') as $item) {
                del_dir($runtimePath . $item);
                if ($item == 'html') {
                    /*$header = [
                        'X-Auth-Email: yeseweb@gmail.com',
                        'X-Auth-Key: 2d619a9968d28ebf436e2ca04ce874d5b806a',
                        'Content-Type: application/json',
                    ];
                    $data = json_encode(['purge_everything' => true]);
                    curl_post('https://api.cloudflare.com/client/v4/zones/9dc53ca7ef618c6e8029034d9171db7d/purge_cache', $data, $header);*/
                }
            }
            Cache::clear();
            $this->success('清空成功');
        } else {
            $this->error('请在系统设置中选择需要清除的缓存类型');
        }
    }
}
