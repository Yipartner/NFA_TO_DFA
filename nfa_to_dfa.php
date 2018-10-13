<?php


class NFA
{
    public $status;
    public $bian;
    public $func;
    public $sstatus;
    public $fstatus;

    public function closure($y)
    {
        $arr = [];
        foreach ($y as $x) {
            if ($this->func[$x]['*'] != []) {
                $arr = array_merge($arr, $this->func[$x]['*']);
            }
        }
        if ($arr == []) {
            return $y;
        }
        return array_unique(array_merge($y, $this->closure($arr)));
    }

    public function move($arr,$bian){
        $brr =[];
        foreach ($arr as $a){
            $brr = array_merge($brr,$this->func[$a][$bian]);
        }
        return array_unique($brr);
    }

    public function get_ziji(& $a,&$index){
        $ziji = [
            'y'=>[],
            'n'=>[]
        ];
        array_push($ziji['n'],$this->closure($this->sstatus));
        sort($ziji['n'][0]);
        while ($ziji['n']!=[]){
            $ziji['y']=array_merge($ziji['y'],$ziji['n']);
            $lzijin=$ziji['n'];
            $ziji['n']=[];
            foreach ($this->bian as $bian){
                foreach ($lzijin as $zi){

                    $U = $this->closure($this->move($zi,$bian));
                    sort($U);
                    if (!in_array($zi,$index)){
                        array_push($index,$zi);
                    }
                    if (!in_array($U,$index)){
                        array_push($index,$U);
                    }
                    if (isset($a[array_search($zi,$index)])){
                        $a[array_search($zi,$index)][$bian]=array_search($U,$index);
                    }
                    else {
                        $a=array_merge($a,[
                            array_search($zi,$index)=>[
                                $bian=>array_search($U,$index)
                            ]
                        ]);
                    }
                    if (!in_array($U,$ziji['y'])){
                        array_push($ziji['n'],$U);

                    }
                }
            }
        }
        return $ziji['y'];
    }
}
//$bbb= new NFA();
//$bbb->status=["0", "1", "2", "3", "4"];
//$bbb->bian = ["a", "b"];
//$bbb->func=[
//
//];
$nfa = new NFA();
$nfa->status = ['0', '1', '2', '3', '4'];
$nfa->bian = ['a', 'b'];
$nfa->func = [
    '0' => [
        'a' => [],
        'b' => [],
        '*' => ['1', '7']
    ],
    '1' => [
        'a' => [],
        'b' => [],
        '*' => ['2', '4']
    ],
    '2' => [
        'a' => ['3'],
        'b' => [],
        '*' => []
    ],
    '3' => [
        'a' => [],
        'b' => [],
        '*' => ['6']
    ],
    '4' => [
        'a' => [],
        'b' => ['5'],
        '*' => []
    ],
    '5' => [
        'a' => [],
        'b' => [],
        '*' => ['6']
    ],
    '6' => [
        'a' => [],
        'b' => [],
        '*' => ['1', '7']
    ],
    '7' => [
        'a' => ['8'],
        'b' => [],
        '*' => ['2', '4']
    ],
    '8' => [
        'a' => [],
        'b' => ['9'],
        '*' => []
    ],
    '9' => [
        'a' => [],
        'b' => ['10'],
        '*' => []
    ],
    '10' => [
        'a' => [],
        'b' => [],
        '*' => []
    ]
];
//设置NFA初态集
$nfa->sstatus=['0'];
//设置NFA终态集
$nfa->fstatus=['10'];

// DFA映射数组
$func = [];
// NFA子集数组
$index = [];
//获取子集
$nfa_ziji=$nfa->get_ziji($func,$index);
foreach ($func as $k => $v){
    $func['T'.$k]=$v;
    unset($func[$k]);
    foreach ($v as $a => $b){
        $func['T'.$k][$a]= 'T'.$b;
    }
}
foreach ($index as $k => $v){
    $index['T'.$k]=$v;
    unset($index[$k]);
}
//echo "NFA子集:",PHP_EOL;
//print_r($nfa_ziji);
echo "DFA_K集合：";
echo json_encode($index),PHP_EOL;
echo "DFA_∑ 集合：";
echo json_encode($nfa->bian),PHP_EOL;
echo "DFA_f集合：";
echo json_encode($func),PHP_EOL;

$dfa_sstatus = [];
$dfa_fstatus = [];
//为集合命名
foreach ($index as $k=>$v){
    if (array_intersect($v,$nfa->sstatus) != []){
        array_push($dfa_sstatus,$k);
    }
    if (array_intersect($v,$nfa->fstatus) != []){
        array_push($dfa_fstatus,$k);
    }
}
echo "DFA 初态集：";
echo json_encode($dfa_sstatus),PHP_EOL;
echo "DFA 终态集：";
echo  json_encode($dfa_fstatus),PHP_EOL;
/*********************************************************NFA_TO_DFA 完成******************************************************************************/
/*
 * K :$index
 * ∑ :$nfa->bian
 * f :$func
 * 初态集：$dfa_sstatus
 * 终态集：$dfa_fstatus
 * */

//1. 分割为非终态集与终态集
$K = [];
foreach ($index as $k => $v){
    array_push($K,$k);
}
$I0 = array_diff($K,$dfa_fstatus);
$I1 = $dfa_fstatus;
$P = [$I1,$I0];
$W = [$I1];
while ($W != []){
    $kkk=array_rand($W,1);
    $A=$W[$kkk];
    unset($W[$kkk]);
    foreach ($nfa->bian as $item){
        $X = getX($func,$item,$A);

        foreach ($P as $k=>$Y){
            $XJY=array_intersect($X,$Y);
            $XCY=array_diff($Y,$X);
            if ($XJY && $XCY){
                unset($P[$k]);
                array_push($P,$XJY);
                array_push($P,$XCY);
                if (in_array($Y,$W)){
                    unset($W[array_search($Y,$W)]);
                    var_dump(in_array($Y,$W));
                    array_push($W,$X);
                    array_push($W,$Y);
                }else{
                    if (count($XJY)<=count($XCY)){
                        array_push($W,$XJY);
                    }
                    else{
                        array_push($W,$XCY);
                    }
                }
            }
        }
    }
}


function getX($f,$b,$a){
    $arr = [];
//    if (!$a){
//        $a=[];
//    }
    foreach ($f as $k=>$v){

        if (in_array($v[$b],$a)){
            array_push($arr,$k);
        }
    }
    return $arr;
}
echo "**********",PHP_EOL;
echo json_encode($P),PHP_EOL;