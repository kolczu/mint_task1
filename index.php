<?php
declare(strict_types=1);

class NestedTree
{
    const CAT_ID_DONT_EXIST = 'Dla podanego id brak kategorii na liscie';
    protected $tree;
    protected $list;

    /**
     * NestedTree constructor.
     * @param string $tree
     * @param string $list
     */
    public function __construct(string $tree, string $list)
    {
        $this->tree = json_decode(file_get_contents($tree), true);
        $this->list = json_decode(file_get_contents($list), true);
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        return $this->iteratNode($this->tree);
    }

    /**
     * @param array $arr
     * @return array
     */
    private function iteratNode(array $arr): array
    {
        foreach ($arr as $key => $value) {
            if (!empty($value) && $value['id']) {
                $arr[$key]['name'] = array_values(array_filter($this->list, function($list) use ($value) {
                    return $list['category_id'] == $value['id'];
                }))[0]['translations']['pl_PL']['name'] ?? self::CAT_ID_DONT_EXIST;
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