<?php

namespace app\modules\common\models;

//TreeBuilder
class Tree {
    /***
     * Base in data
     * @var array
     */
    protected $data = array();

    protected $relations = array();

    /***
     * Tree data
     * @var array
     */
    protected $tree = array();

    /***
     * Array of items for tree building
     * @param array $data
     */
    public function __construct($data = array()) {
        $this->data = $data;
    }


    public function build() {
        $items = &$this->getData();
        $this->buildDescendantsRelations();
        foreach ($items as $k => $v) {
            if ($v['parent_id'] === NULL) {
                array_push($this->tree, $this->buildNodeTree($k, $this->relations));
            }
        }
        return $this->tree;
    }

    public function buildNodeTree($root_id = 0, &$descendants = array()) {
        $tree = array(
            'id' => $root_id,
        );
        if(isset($descendants[$root_id])) {
            foreach ($descendants[$root_id] as &$item) {
                $tree['descendants'][] =  $this->buildNodeTree($item, $descendants);
            }
            unset($item);
        }
        return $tree;
    }

    /*
     * Build descendants relations
     * Array root_id => array children_id
     * */
    public function buildDescendantsRelations() {
        $items = &$this->getData();
        foreach ($items as $k => $v) {
            $id = ($v['parent_id'] === NULL) ? NULL : (int)$v['parent_id'];
            if ($id !== NULL) {
                if(!isset($this->relations[$id])) {
                    $this->relations[$id] = array();
                }
                array_push($this->relations[$id], $k);
            }
        }
    }

    public function getTree() {
        return $this->tree;
    }

    public function &getRelations() {
        return $this->relations;
    }

    protected function &getData() {
        return $this->data;
    }
}