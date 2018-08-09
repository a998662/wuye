<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/4/16
 * Time: 16:59
 */

namespace app\validate;


use think\Validate;

class User extends Validate {
    protected $rule = [
        //'verify|验证码'   => 'require|captcha',
        'username|用户名' => 'require|email',
        'nickname|昵称'  => 'require|chs|length:2,6',
        'password|密码'  => 'require',
        'repassword'   => 'require|confirm:password',

    ];

    protected $message = [
        'username.email'   => '账号只能为邮箱格式',
        'username.require' => '用户名不能为空',
        'nickname.require' => '昵称不能为空',
        'nickname.length'  => '昵称长度只能在2~6个汉字之内',
        'nickname.chs'     => '昵称只能为汉字',
        'password.require' => '密码不能为空',
        'repassword'       => '两次输入的密码不一致',
    ];

}