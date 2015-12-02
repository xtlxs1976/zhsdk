<?php

namespace zh;

/*
    文件上传类，V1.0版
    * 已支持功能
        + 指定上传文件路径
        + 多文件上传
        + 重命名文件
            - GUID方式
            - 文件名+ _TIMESTAMP 方式
        + 文件名编码转换

    还需增加其它功能
        + 文件大小限制
        + 文件格式限制
        + 应改成开放式的设计，如 过滤器的设置 ，允许用户自定义过滤器

*/
class FileUploadHelper{
    // 配置私有变量
    private $uploadDir;
    private $varName;
    private $changeFileName;
    private $clientEncoding;
    private $serverEncoding;
    private $autoOverride;
    // 上传信息数组
    private $upload_info;

    // 支持配置格式的数组
    // 如果配置不成功会抛出异常 Exception
    /*
        config([
               'uploadDir' =>'F:/www/upload',
               'varName' => 'UserFile',   //上传文件使用的变量名称
               'changeFileName' => true,
               'clientEncoding' => 'GBK',      // 指的是客户端的网页编码，而非用户操作系统编码
               'serverEncoding' => 'UTF-8',    // 指的是服务器操作系统编码，
        ]);
    */
    public function config($cfg){
        if(!\is_array($cfg)){
            throw new Exception('Param must be array!');
        }

        // 设置上传文件路径
        if(empty($cfg['uploadDir'] )){
            $this->uploadDir = './'; // 如果没指定，则是当前路径
        }
        else{
            // 检查上传路径是否正确
            $dirstr = \trim($cfg['uploadDir']);

            if( \is_dir($dirstr)){
                // 格式化成以 / 结束的路径形式
                if(\substr($dirstr,-1) != '/'){
                    $dirstr .= '/';
                }
                $this->uploadDir = $dirstr;
            }
            else{
                throw new Exception('Error dir supplied !');
            }
        }

        // 设置上传文件控件名称
        if(empty($cfg['varName'] )){
            throw new Exception('Must setting file upload control name!');
        }
        else{
            $this->varName = \trim($cfg['varName']);
        }

        // 设置是否改变文件名称,如设置改变文件名称，则以下三项不需设置
        $this->changeFileName = ! empty( $cfg['changeFileName'] );

        // 设置客户端网页编码，浏览器会根据编码发送对应字符编码的文件名
        if(empty($cfg['clientEncoding'])){
            $this->clientEncoding = 'UTF-8';
        }
        else{
            $this->clientEncoding = \strtoupper( \trim($cfg['clientEncoding']) );
        }
        //服务端的编码，如未提供，则视为utf-8
        if(empty($cfg['serverEncoding'])){
            $this->serverEncoding = 'UTF-8';
        }
        else{
            $this->serverEncoding = \strtoupper( \trim($cfg['serverEncoding']) );
        }
        //当文件名相同时是否自动覆盖，一般是不覆盖
        if(empty($cfg['autoOverride'])){
            $this->autoOverride = false;
        }
        else{
            $this->autoOverride = true;
        }

    }


    public function process()
    {
        $this->upload_info = []; //clear
        $fc = $this->get_files_count();

        switch($fc){
            case 0:
                return false;
            case 1:
                $errno = $_FILES[$this->varName]['error'];
                if($errno != 0){
                    throw new Exception($this->get_error_info($errno));
                }
                else{
                    $cilent_filename = \basename($_FILES[$this->varName]['name']);
                    $server_filename  = $this->process_file_name($cilent_filename);

                    if(file_exists($this->uploadDir . $server_filename)){
                        if(!$this->autoOverride){ //如果不自动覆盖
                            $server_filename .= '_' . time();  //在文件名后加一个时间戳
                        }
                    }

                    $tmpfile = $_FILES[$this->varName]['tmp_name'];
                    if(\is_uploaded_file($tmpfile)){
                        if(!\move_uploaded_file($tmpfile, $this->uploadDir . $server_filename)){
                            throw new Exception('Move file failed!');
                        }
                    }
                    else{
                        throw new Exception('Found a file not uploaded by POST!');
                    }

                    $this->upload_info[] = [
                            'cilent_filename'=>$cilent_filename,
                            'server_filename'=>$server_filename,
                        ];
                }
                return true;

            default:
                for($i =0; $i<$fc; $i++){
                    $errno = $_FILES[$this->varName]['error'][$i];
                    if($errno != 0){
                        throw new Exception($this->get_error_info($errno));
                    }
                    else{
                        $cilent_filename = \basename($_FILES[$this->varName]['name'][$i]);
                        $server_filename  = $this->process_file_name($cilent_filename);

                        if(file_exists($this->uploadDir . $server_filename)){
                            if(!$this->autoOverride){ //如果不自动覆盖
                                $server_filename .= '_' . time();  //在文件名后加一个时间戳
                            }
                        }

                        $tmpfile = $_FILES[$this->varName]['tmp_name'][$i];
                        if(\is_uploaded_file($tmpfile)){
                            if(!\move_uploaded_file($tmpfile, $this->uploadDir . $server_filename )){
                                throw new Exception('Move file failed!');
                            }
                        }
                        else{
                            throw new Exception('Found a file not uploaded by POST!');
                        }

                        $this->upload_info[] = [
                                'cilent_filename'=>$cilent_filename,
                                'server_filename'=>$server_filename,
                            ];
                    }
                }
                return true;

        } //switch

    }

    //取上传文件信息
    public function get_upload_info(){
        return $this->upload_info;
    }

    public function get_config(){
        return [
             'uploadDir' => $this->uploadDir,
             'varName' => $this->varName,
             'changeFileName' => $this->changeFileName,
             'clientEncoding' => $this->clientEncoding,
             'serverEncoding' => $this->serverEncoding,
             'autoOverride' => $this->autoOverride,
             ];
    }

    private function process_file_name($filename)
    {
        if($this->changeFileName){
            return $this->get_guid_string();
        }
        else{
            return $this->convert_filename($filename);
        }

    }

    //生成guid
    private function get_guid_string()
    {
        $str = \md5(\uniqid(\mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $uuid;
    }

    //转换文件名编码,如果编码相同则不需转换
    private function convert_filename($filename)
    {
        if(\strcmp($this->clientEncoding,$this->serverEncoding)){
            return \mb_convert_encoding($filename, $this->serverEncoding,$this->clientEncoding);
        }
        return $filename;
    }

    // 检查上传的文件数量
    private function get_files_count(){
        if(isset($_FILES[$this->varName]['tmp_name'])){
            if(is_array($_FILES[$this->varName]['tmp_name'])){
                return count($_FILES[$this->varName]['tmp_name']);
            }
            else{
                return 1;
            }
        }
        return 0;
    }

    // 取上传文件错误信息
    private function get_error_info($errno)
    {
        switch ($errno) {
            case UPLOAD_ERR_OK :
                return '文件上传成功。';
            case UPLOAD_ERR_INI_SIZE:
                return '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。';
            case UPLOAD_ERR_FORM_SIZE:
                return '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。';
            case UPLOAD_ERR_PARTIAL:
                return '文件只有部分被上传。';
            case UPLOAD_ERR_NO_FILE:
                return '没有文件被上传。';
            case UPLOAD_ERR_NO_TMP_DIR:
                return '找不到临时文件夹。';
            case UPLOAD_ERR_CANT_WRITE:
                return '文件写入失败。';
            default:
                return '未知错误。';
        }
    }

}


/*
示例代码
header('content-type:text/HTML;charset=utf-8');
try{
    $fu = new FileUploadHelper();
    $fu->config([
               'uploadDir' => 'F:/www/upload',
               'varName' => 'UserFile',
               // 'changeFileName' => true,

               // 'clientEncoding' => 'UTF-8',
               'serverEncoding' => 'GB2312',
               'autoOverride' => true,
        ]);

    if($fu->process()){
        echo '<pre>';
        var_dump( $fu->get_upload_info() );
        echo '\n';
        var_dump( $fu->get_config() );
        echo '</pre>';
    }
}
catch(Exception $ex){
    echo $ex->getMessage();
}

*/
