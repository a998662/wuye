<?php
namespace tree;
class Tree
{
    private $in_data = null;
    private $out_data = null;
    private $key = null;
    private $parent = null;
    private $icon = null;

    function __construct($list, $key, $parent, $icon = array('│', '├', '└'))
    {
        $this->in_data = $list;
        $this->key = $key;
        $this->parent = $parent;
        $this->icon = $icon;
    }

    function convert($start_id = 0, $remove_id = null)
    {
        $this->out_data = array();
        $this->get($start_id, $remove_id, 0);
        return $this->out_data;
    }

    private function get($start_id, $remove_id, $dept)
    {
        $dept++;
        $temp = array();
        foreach ($this->in_data as $value) {
            $kid = $value[$this->key];
            $pid = $value[$this->parent];
            if ($remove_id != null && $kid == $remove_id) {
                continue;
            }
            if ($pid == $start_id) {
                $temp[] = $value;
            }
        }
        $count = count($temp);
        foreach ($temp as $key => $value) {
            $kid = $value[$this->key];
            $value['path'] = $this->path($count, $key, $dept);
            $this->out_data[] = $value;
            $this->get($kid, $remove_id, $dept);
        }
    }

    private function path($count, $idx, $dept)
    {
        $icons = array();
        for ($i = 1; $i < $dept; $i++) {
            $icons[] = $this->icon[0];
        }
        if (count($this->out_data) > 1 && $count == ($idx + 1)) {
            $icons[] = $this->icon[2];
        } else {
            $icons[] = $this->icon[1];
        }
        return join(' ', $icons);
    }
}
