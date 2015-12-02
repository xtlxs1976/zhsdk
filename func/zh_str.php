<?php

// -----------------------------------------------------------------------------
// 返回文件尺寸字符串表示，类似于 7KB 等
function filesize_str($filesize){
    if($filesize === 0){
        $sizestr = '0 KB';
    }
    else if($filesize < 1024 * 1024){
        $sizestr = ceil($filesize / 1024) . 'KB';
    }
    else if($filesize < 1024 * 1024 *1024 ){
        $sizestr = ceil($filesize / (1024*1024) ) . 'MB';
    }
    else if($filesize < 1024 * 1024 * 1024 * 1024 ){
        $sizestr = ceil($filesize / (1024*1024*1024) ) . 'GB';
    }
    return $sizestr;
}

// -----------------------------------------------------------------------------
// 返回数字千分位表示
// function number_format($number) // php自带有此函数
// windows 下不支持 money_format
function money_str($number)
{
    return number_format($number,2);
}

// --------------------------·---------------------------------------------------
// 返回文件名
function filename($path)
{
    return \pathinfo($path,PATHINFO_FILENAME);
}

// 返回文件后缀名
function extname($path)
{
    return \pathinfo($path,PATHINFO_EXTENSION);
}
