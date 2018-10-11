<?php


class NFA
{
    public $status;
    public $bian;
    public $func;
    public $sstatus;
    public $fstatus;
    public $ziji;
    public $nameNum;

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

    public function get_ziji(){
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
$nfa->sstatus=['0'];
$nfa->fstatus=['10'];

//print_r($nfa->closure(['3', '8']));

//print_r($nfa->closure($nfa->move($nfa->closure(['0']),'a')));

print_r($nfa->get_ziji());
