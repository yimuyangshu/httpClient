<?php

namespace Yimu\PlugLib;

class Functions {
    /**
     * 小数点后保留位数,不够补充0
     * $num 数字
     * $length 需要(保留/补齐)的位数
     */
    public function roundingDecimal($num,$length)
    {
        $num = strpos($num, '.') > 0 ? substr($num, 0, strpos($num, '.') + $length+1) : $num;
        return sprintf("%.".$length."f",$num);
    }

    /**
     * 通过回车符分割成数组
     * @param $str 包含回车符的字符串
     * @return false|string[]
     *
     */
    public function _explode($str) {
        $arr = explode("\n", $str);
        $arr = array_map('trim', $arr);
        $arr = array_filter($arr, function($item){
            return ($item && 1);
        });
        return array_unique($arr);
    }

    /**
     * 图片转Base64
     * @param $image_file
     * @return string
     */
    public function base64EncodeImage ($image_file) {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}