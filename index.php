<?php

class NestedTree
{
    protected $tree;
    protected $list;

    public function __construct($tree, $list)
    {
        $this->tree = json_decode(file_get_contents($tree), true);
        $this->list = json_decode(file_get_contents($list), true);
    }

    public function execute()
    {
        return $this->iteratNode($this->tree);
    }

    private function iteratNode($arr)
    {
        foreach ($arr as $key => $value) {
            if (!empty($value) && $value['id']) {
                $arr[$key]['name'] = $value['id'];
                foreach ($this->list as $list) {
                    if ($list['category_id'] == $value['id']) {
                        $arr[$key]['name'] = $list['translations']['pl_PL']['name'];

                    }
                }
            }

            if (is_array($value) && !empty($value)) {
                $arr[$key] = $this->iteratNode($arr[$key]);
            }

            if (!is_array($value)) {
                $arr[$key] = (string)$value;
            }
        }

        return $arr;
    }
}
$treeFile = 'tree.json';
$listFile = 'list.json';

$tree = new NestedTree($treeFile, $listFile);
$tree = $tree->execute();
echo '<pre>';
print_r($tree);