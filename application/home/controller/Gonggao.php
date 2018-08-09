<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/7/28
 * Time: 13:28
 */

namespace app\home\controller;


use think\Db;

class Gonggao extends Index {
    public function index(){
        $list = Db::name('placards')->where('status',1)->order('id desc')->paginate(18, false, [
            'path' => "/placard/list-[PAGE].html"
        ]);


        $title = '系统公告';
        $seo   = [
            'title'       => $title . '-' . config('web_site_title'),
            'keywords'    => $title . '-' . config('web_site_title'),
            'description' => $title . '-' . config('web_site_title'),
        ];

        $this->assign($seo);
        $this->assign('list', $list);
        return $this->fetch();
    }


    public function info(){
        $id = input('id/d');
        $info = Db::name('placards')->find($id);

        $this->assign($info);
        return $this->fetch();
    }
}