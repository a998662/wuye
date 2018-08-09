<?php
/**
 * Created by PhpStorm.
 * User: 65998
 * Date: 2018/6/21
 * Time: 18:00
 */

namespace app\home\controller;


class Help extends Index {
    public function ios(){
        $group = input('group') ?: 'index';

        $this->assign('group', $group);
        return $this->fetch();
    }

    public function anzuo(){
        return $this->fetch();
    }

    public function pc(){
        return $this->fetch();
    }

    public function collec(){
        return $this->fetch();
    }
}