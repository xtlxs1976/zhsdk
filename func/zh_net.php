<?php

//----------------------------------------------------------------------------------------
// 永久重定向（状态码301）
function link_to($url)
{
    header( "HTTP/1.1 301 Moved Permanently" ) ;
    header( 'Location: ' . $url );
}

//----------------------------------------------------------------------------------------
//重定向（状态码302）
//如果是本网站，不用加http,如果是外网，需加http
//locate('index/foo');
//locate('http://www.sina.com.cn');
function locate($url)
{
    header('Location:'.$url);
    exit;
}

//----------------------------------------------------------------------------------------
//页面不存在（状态码404）
function no_this_page()
{
    header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
}

//----------------------------------------------------------------------------------------
//设置当前生成页面的字符集
function set_charset($charset = 'utf-8')
{
    header('content-type:text/HTML;charset='. $charset);
}


//----------------------------------------------------------------------------------------
// 下载文件
// server_filename 服务器的真实名称
// client_filename 客户端的名称
// sendfile('upload/we231231244','my.jpg');
//
function downfile($server_filename,$client_filename='')
{
    if(false === file_exists($server_filename)){
        return false;
    }

    if(empty($client_filename )){   //如果未设置 client_filename
        $client_filename = basename($server_filename);
    }

    header('Content-Type: application-x/force-download' );
    header('Content-Length: '.filesize($server_filename));
    header('Content-Disposition: attachment; filename=' . $client_filename );
    header('Content-Transfer-Encoding: binary');
    readfile($server_filename);
}


//----------------------------------------------------------------------------------------
// 取客户端IP
function get_client_ip() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = "unknown";
    return ($ip);
}

//----------------------------------------------------------------------------------------
// 取服务环境信息
function get_server_info()
{
    return [
        'OS' => php_uname('s'),
        'OS Version' => php_uname('r'),
        'OS Full String' => php_uname('s') . ' ' .php_uname('r'). ' ' . php_uname('v'),

        'PHP Versioin' => PHP_VERSION,

        'CurrentUser'=>get_current_user(),

    ];

}
//----------------------------------------------------------------------------------------
// 取客户端浏览器
function get_client_brower()
{
    if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.0"))
       echo "Internet Explorer 8.0";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 7.0"))
       echo "Internet Explorer 7.0";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.0"))
       echo "Internet Explorer 6.0";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/3"))
       echo "Firefox 3";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox/2"))
       echo "Firefox 2";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"Chrome"))
       echo "Google Chrome";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"Safari"))
       echo "Safari";
       else if(strpos($_SERVER["HTTP_USER_AGENT"],"Opera"))
       echo "Opera";
       else echo $_SERVER["HTTP_USER_AGENT"];
}
