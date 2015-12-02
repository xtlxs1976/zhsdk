<?php

namespace zh;
//----------------------------------------------------------------------------------------
// 基本处理和配置

// 版本需求
if(PHP_VERSION < '5.3'){
    exit('PHP Version too old!');
}

//加载核心配置
require(__DIR__. '/config/config.php');

// 错误处理
// 将未处理错误重新抛出一个异常
set_error_handler(function ($errno ,$errstr ,$errfile , $errline , $errcontext ){
    throw new \ErrorException($errstr,0,$errno,$errfile,$errline);
    return true;
});

// 异常处理
set_exception_handler(
    function($ex){
        function show_exception_msg($ex)
        {
            echo ' ------ ZHSDK Exception Report ------ ' . PHP_EOL ;
            echo ' Code:    ' . $ex->getCode() . PHP_EOL .
                 ' File:    ' . $ex->getFile() . PHP_EOL .
                 ' Line:    ' . $ex->getLine() . PHP_EOL .
                 ' Message: ' . $ex->getMessage() . PHP_EOL ;
            echo ' Trace:   ' . PHP_EOL ;
            echo $ex->getTraceAsString();

            $pex = $ex->getPrevious();
            if($pex){
                show_exception_msg($pex);
            }
        }

        show_exception_msg($ex);
    }
);

// 中文时区
date_default_timezone_set(zh::TimeZone);


//启用session
session_start();


//----------------------------------------------------------------------------------------
// 加载常用函数，所有的函数都在全局空间里
require __DIR__ . '/func/zh_comm.php';
require __DIR__ . '/func/zh_str.php';
require __DIR__ . '/func/zh_net.php';



//----------------------------------------------------------------------------------------
// 设定自动加载机制，以便引入对应的功能类
//    zhsdk/class/classname.php
spl_autoload_register(
    function($className){
        $filename = basename($className);

        $fullname = __DIR__ . '/class/'. $filename . '.class.php';

        if(is_readable($fullname)){   //如果有此文件则包含进来
            require $fullname;
        }
    }
);
