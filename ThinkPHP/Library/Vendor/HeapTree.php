<?php
/**
 *@author:qgy
 *@version:1.0
 *@date:2016-06-01
 *基于小顶堆的的取top的排序操作
 */

class HeapTree{

    public $arr=array();
    public $top;

    public function __construct($top){
        $this->top=$top;
    }

    public function init($arr,$colume){
        //计算出最开始的下标$index,如图,为数字"97"所在位置,比较每一个子树的父结点和子结点,将最小值存入父结点中
        //从$index处对一个树进行循环比较,形成最小堆
        $arrSize = count($arr);
        for($index=intval($arrSize/2)-1; $index>=0; $index--){
            //如果有左节点,将其下标存进最小值$min
            if($index*2+1<$arrSize){
                $min=$index*2+1;
                //如果有右子结点,比较左右结点的大小,如果右子结点更小,将其结点的下标记录进最小值$min
                if($index*2+2<$arrSize){
                    if($arr[$index*2+2][$colume]>$arr[$min][$colume]){
                        $min=$index*2+2;
                    }
                }
                //将子结点中较小的和父结点比较,若子结点较小,与父结点交换位置,同时更新较小
                if($arr[$min]>$arr[$index]){
                    self::swap($min,$index);
                }   
            }
        }
        $this->arr = $arr;
    }

    //添加数据
    public function add($item,$index,$attr){
        $len=count($this->arr);
        foreach ($this->arr as $key => $val) {
            if($val[$index]==$item[$index] && $val[$attr]==$item[$attr]){
                return ;
            }
        }
        if($len < $this->top){
            array_push($this->arr, $item);
            $this->adjust(0,$attr);
        }else{
            if($this->arr[0][$attr] < $item[$attr]){
                if($len >= $this->top){
                    $this->arr[0]=$item;
                }else{
                    array_unshift($this->arr, $item);
                }
                $this->adjust(0,$attr);
            }
        }
    }

    //调整数据
    public function adjust($num,$attr){
        $lchild=$num * 2 +1;
        $rchild=$num * 2 +2;
        if(isset($this->arr[$lchild])){
            $tempmin=(($this->arr[$num][$attr] > $this->arr[$lchild][$attr]) ? $num : $lchild);
            if(isset($this->arr[$rchild])){
                $tempmin= ($this->arr[$tempmin][$attr] > $this->arr[$rchild][$attr]) ? $tempmin : $rchild;
            }
            self::swap($num,$tempmin);
            //递归对左右子树进行操作
            self::adjust($lchild,$attr);
            self::adjust($rchild,$attr);
        }
        else{
            return $this->arr;
        }
    }

    //获取属性
    public function getarr(){
        return $this->arr;
    }

    //交换
    private function swap($one,$another){
        $tmp=$this->arr[$one];
        $this->arr[$one]=$this->arr[$another];
        $this->arr[$another]=$tmp;
        return $this->arr;
    }

    

    
}
