<?php

namespace zh;

//----------------------------------------------------------------------------------------
// 基于 PDO 专门对mysql数据库处理
// 客户端自动设置为 utf-8
class PDOMysql extends \PDO
{
    private $dbh;
    private $dbname;

    public function __construct
        (
            $dbname,
            $host = 'localhost',$port=3306,
            $username = 'root',$password=''
        )
    {
        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname .';charset=utf8';

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_PERSISTENT => true,         //使用持久连接

        );

        $this->dbname = $dbname;

        parent::__construct($dsn,$username,$password,$options);

        $this->setAttribute(parent::ATTR_ERRMODE,parent::ERRMODE_EXCEPTION); //设置错误模式为抛出异常

    }

}
