<?php

namespace page;

use think\Paginator;

class Page extends Paginator {

    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            return '<li><a href="' . $this->url(1) . '" title="首页">首页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="首页">首页</a></li>';
        }
    }

    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return '<li><a href="' . $this->url($this->currentPage - 1) . '" title="上一页">上一页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="上一页">上一页</a></li>';
        }
    }

    //下一页
    protected function next() {
        if ($this->hasMore) {
            return '<li><a href="' . $this->url($this->currentPage + 1) . '" title="下一页">下一页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="下一页">下一页</a></li>';
        }
    }


    //尾页
    protected function last() {
        if ($this->hasMore) {
            return "<li><a href='" . $this->url($this->lastPage) . "' title='尾页'>尾页</a></li>";
        } else {
            return '<li><a href="javascript:;" title="尾页">尾页</a></li>';
        }
    }


    //首页
    protected function home1() {
        if ($this->currentPage() > 1) {
            return '<li><a href=javascript:pagination("' . $this->url(1) . '") title="首页">首页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="首页">首页</a></li>';
        }
    }

    //上一页
    protected function prev1() {
        if ($this->currentPage() > 1) {
            return '<li><a href=javascript:pagination("' . $this->url($this->currentPage - 1) . '") title="上一页">上一页</a></li>';
        } else {
            return '<li><a data-href="0" href="javascript:;" title="上一页">上一页</a></li>';
        }
    }

    //下一页
    protected function next1() {
        if ($this->hasMore) {
            return '<li><a href=javascript:pagination("' . $this->url($this->currentPage + 1) . '") title="下一页">下一页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="下一页">下一页</a></li>';
        }
    }


    //尾页
    protected function last1() {
        if ($this->hasMore) {
            return '<li><a href=javascript:pagination("' . $this->url($this->lastPage) . '") title="尾页">尾页</a></li>';
        } else {
            return '<li><a href="javascript:;" title="尾页">尾页</a></li>';
        }
    }

    //统计信息
    protected function info() {
        return '<li class="visible-xs"><span class="num">' . $this->currentPage . '/' . $this->lastPage . '</span></li>';
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
     * 渲染分页html
     * @return mixed
     */
    public function render() {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf('<ul class="cleafix">%s %s %s %s %s</ul>%s', $this->home1(), $this->prev1(),$this->info(), $this->next1(), $this->last1(), $this->js());
            } else {
                return sprintf('<ul class="cleafix">%s %s %s %s %s %s</ul>', $this->home(), $this->prev(), $this->getLinks(), $this->info(), $this->next(), $this->last());
            }
        }
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function renders() {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf('<ul class="cleafix">%s %s %s %s %s</ul>%s', $this->home1(), $this->prev1(), $this->info(), $this->next1(), $this->last1(), $this->js());
            }else{
                return sprintf('<ul class="cleafix">%s %s %s %s %s</ul>%s', $this->home1(), $this->prev1(), $this->info(), $this->next1(), $this->last1(), $this->js());
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page) {
        return '<li class="hidden-xs"><a href="' . htmlentities($url) . '" title="第"' . $page . '"页" >' . $page . '</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text) {
        return '<li class="hidden-xs"><a href="javascript:;">' . $text . '</a></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text) {
        return '<li class="hidden-xs active"><a href="javascript:;" class="active">' . $text . '</a></li>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots() {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls) {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page) {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    protected function js() {
        return '<script>
        function pagination(link) {
            link = api_host + link;
            $.post(link,{id:vid},function (res) {
                $("#comment_list").html(res);
            })
        }
        </script>';
    }

    /**
     * 分页样式
     */
    protected function css() {
        return '  <style type="text/css">
            .pagination p{
                display:inline-block !important;
                cursor:pointer;
                margin:0;
            }
            .pagination{
                margin: 0;
                width: 100%;
                text-align: center;
            }
            .pagination a{
                display:inline-block !important;
                margin-right:10px;
                padding:2px 12px;
                border:1px #cccccc solid;
                background:#fff;
                text-decoration:none;
                color:#808080;
                font-size:12px;
                line-height:24px !important;
            }
            .pagination a:hover{
                color:#1db69a;
                background: white;
                border:1px #1db69a solid;
            }
            .pagination a.cur{
                border:none;
                background:#1db69a;
                color:#fff;
                border: 1px #1db69a solid;
            }
            .pagination p{
                padding:2px 12px;
                font-size:12px;               
                line-height:24px !important;
                color:#bbb;
                border:1px #ccc solid;
                background:#fcfcfc;
                margin-right:8px;

            }
            .pagination p.pageRemark{
                border-style:none;
                background:none;
                margin-right:0px;
                padding:4px 0px;
                color:#666;
            }
            .pagination p.pageRemark b{
                color:#1db69a;
            }
            .pagination p.pageEllipsis{
                border-style:none;
                background:none;
                padding:4px 0px;
                color:#808080;
            }
            .dates li {font-size: 14px;margin:20px 0}
            .dates li span{float:right}
        </style>';
    }
}