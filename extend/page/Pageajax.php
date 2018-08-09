<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 二当家的 <zuojiazi@vip.qq.com> <http://www.erdangjiade.com>
// +----------------------------------------------------------------------

namespace page;

use think\Paginator;

class Pageajax extends Paginator{

    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            return '<li><a href="javascript:paginate(\''.$this->url(1).'\');" title="首页">首页</a></li>';
        }else{
            return '<li><a href="javascript:;" title="首页">首页</a></li>';
        }
    }

    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return '<li><a href="javascript:paginate(\''.$this->url($this->currentPage - 1).'\');" title="上一页">上一页</a></li>';
        }else{
            return '<li><a href="javascript:;" title="上一页">上一页</a></li>';
        }
    }

    //下一页
    protected function next() {
        if ($this->hasMore) {
            return '<li><a href="javascript:paginate(\''.$this->url($this->currentPage + 1).'\');" title="下一页">下一页</a></li>';
        }else{
            return '<li><a data-page="0" href="javascript:;" title="下一页">下一页</a></li>';
        }
    }

    //尾页
    protected function last() {
        if ($this->hasMore) {
            return '<li><a href="javascript:paginate(\''.$this->url($this->lastPage).'\');" title="尾页">尾页</a></li>';
        }else{
            return '<li><a href="javascript:;" title="尾页">尾页</a></li>';
        }
    }
    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks() {
        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 2;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 1);
            $block['last']  = $this->getUrlRange($this->lastPage, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 1);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 1), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 1);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="hidden-xs"><a href="javascript:;" class="active">' . $text . '</a></li>';
    }

    //统计信息
    protected function info() {
        return '<li class="visible-xs"><span class="num">' . $this->currentPage . '/' . $this->lastPage . '</span></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="hidden-xs active"><a title="第'. $text .'页" >' . $text . '</a></li>';
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url,$page)
    {
        return '<li class="hidden-xs"><a href="javascript:paginate(\''.htmlentities($url).'\');" title="第'. $page .'页" >' . $page . '</a></li>';
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url,$page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url,$page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url,$page);
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render() {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf('<ul class="cleafix page-list">%s %s %s %s %s</ul>', $this->home1(), $this->prev1(),$this->info(), $this->next1(), $this->last1());
            } else {
                return sprintf('<ul class="cleafix page-list">%s %s %s %s %s %s</ul>', $this->home(), $this->prev(), $this->getLinks(), $this->info(), $this->next(), $this->last());
            }
        }
    }

    public function renders() {
        // TODO: Implement renders() method.
    }

}
