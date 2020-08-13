<?php
/**
 *@author:qgy
 *@version:1.0
 *@date:2016-06-01
 *排序算法
 */
class Sort{
    //快速排序算法
    public function quickSort($arr,$colume)
    {
        $count = count($arr);   //统计出数组的长度
        if ($count <= 1) { // 如果个数为空或者1，则原样返回数组
            return $arr;
        }
        $index = $arr[0][$colume]; // 把第一个元素作为标记
        $left = [];    //定义一个左空数组
        $right = [];    //定义一个有空数组
        for ($i = 1; $i < $count; $i++) {   //从数组的第二数开始与第一个标记元素作比较
            if ($arr[$i][$colume] > $index) {        //如果小于第一个标记元素则放进left数组
                $left[] = $arr[$i];
            } else {                        //如果大于第一个标记元素则放进right数组
                $right[] = $arr[$i];
            }
        }
        $left  = $this->quickSort($left,$colume);      //把left数组再看成一个新参数，再递归调用，执行以上的排序
        $right = $this->quickSort($right,$colume);     //把right数组再看成一个新参数，再递归调用，执行以上的排序
        return array_merge($left, [$arr[0]], $right);   //最后把每一次的左数组、标记元素、右数组拼接成一个新数组
    }
}
