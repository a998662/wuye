<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/12
 * Time: 18:44
 */

namespace app\home\controller;


use app\model\Likes;
use app\model\Users;
use app\model\Video;
use think\captcha\Captcha;
use think\Db;
use think\facade\Session;

class User extends Index {

    public function info() {
        return $this->fetch();
    }

    public function favorite() {
        return $this->fetch();
    }

    /**
     * 登录
     * @return mixed
     **/
    public function login() {
        return $this->fetch();
    }

    public function logout() {
        Session::delete('user');
        $this->redirect('/user/login');
    }

    /**
     * 注册//信息框
     * layer.open({
     * content: '缺少參數'
     * ,btn: '我知道了'
     * });
     */
    public function register() {

        return $this->fetch();
    }


    // 检测输入的验证码是否正确，$value为用户输入的验证码字符串
    public function checkCode() {
        $value   = input('verifycode');
        $captcha = new Captcha();
        if ($captcha->check($value)) {
            return false;
        }
        return true;
    }
}