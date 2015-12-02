<?php

//----------------------------------------------------------------------------------------
// 创建一个UUID字符串
//$prefix 前缀
function create_uuid($prefix = "")
{
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8) . '-';
    $uuid .= substr($str,8,4) . '-';
    $uuid .= substr($str,12,4) . '-';
    $uuid .= substr($str,16,4) . '-';
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}

//----------------------------------------------------------------------------------------
// 加密一个字符串，返回32位字符串
// $salt     ：盐值，一般使用用户名当盐值
// $password : 需要加密的密文
//
// 原理：
//     利用加盐原理，防止彩虹库攻击
//     先用sha1将username生成盐
//     将password与sha1的字符联接，再用md5生成加密字符
function encry_password($salt,$password)
{
    $shastr  = \sha1(\trim($salt));
    $tmp = $shastr. $password ;
    return \md5($tmp);
}
