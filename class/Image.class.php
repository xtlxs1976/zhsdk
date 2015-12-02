<?php

namespace zh;

class Image
{
    private $im;

    // 黑色
    const Black       =0;
    // 白色
    const White       =0xFFFFFF;
    // 灰色
    const Gray        =0xC0C0C0;
    // 深灰色
    const DarkGray    =0x80808080;
    // 红色
    const Red         =0xFF0000;
    // 深红色
    const DarkRed     =0x800000;
    // 绿色
    const Green       =0x00FF00;
    // 深绿色
    const DarkGreen   =0x008000;
    // 蓝色
    const Blue        =0x0000FF;
    // 深蓝色
    const DarkBlue    =0x000080;
    // 紫红色
    const Magenta     =0xFF00FF;
    // 深紫红
    const DarkMagenta =0x800080;
    // 紫色
    const Cyan        =0x00FFFF;
    // 深紫
    const DarkCyan    =0x008080;
    // 黄色
    const Yellow      =0xFFFF00;
    // 棕色
    const Brown       =0x808000;

    //--------------------------------------------------------------------------------
    // 构造相关
    private function __construct(){}   // 防止new

    public function __destruct()
    {
        imagedestroy($this->im);
    }

    public static function create($width,$height)
    {
        $obj = new self();
        $obj->im = imagecreatetruecolor($width, $height);
        return $obj;
    }

    // 支持 jpg|jpeg|gif|png
    public static function create_from($filename)
    {

        $size = @getimagesize($filename);
        if(FALSE == $size)return NULL;

        list($w, $h, $type) = $size;

        $obj =  new self();

        switch ($type) { // 1 = GIF，2 = JPG，3 = PNG ...
            case 1:  // gif
                $obj ->im = imagecreatefromgif($filename);
                break;
            case 2:  // jpg
                $obj ->im = imagecreatefromjpeg($filename);
                break;
            case 3:  // png
                $obj ->im = imagecreatefrompng($filename);
                break;
            default:
                $obj ->im = FALSE;
                break;
        }

        if( FALSE == $obj->im ){
            return NULL;
        }
        return $obj;

    }
    //--------------------------------------------------------------------------------
    // 基本属性
    public function get_width(){
        return imagesx($this->im);
    }

    public function get_height(){
        return imagesy($this->im);
    }

    // 返回图像文件的句柄，便于图像函数使用
    public function get_handler(){
        return $this->im;
    }

    //--------------------------------------------------------------------------------
    // 用系统提供字体水平输出一行文字，不支持中文
    public function draw_text($x,$y,$text,$color,$font = 5)
    {
        imagestring($this->im, $font, $x, $y, $text, $color);
    }


    // draw_ttftext(100,100,'hello,world!',[
    //         'font' => 'c:/WINDOWS/Fonts/微软雅黑.ttf',
    //         'fontsize' => 20,
    //         'color' => Image::Yellow,
    //     ],30)
    public function draw_ttftext($x , $y ,$text,$style, $angle = 0)
    {
        if(zh::ScriptEncoding <> 'UTF-8'){
            $utfstr = iconv(zh::ScriptEncoding, 'UTF-8', $text);
        }
        else
        {
            $utfstr = $text;
        }

        $font = iconv(zh::ScriptEncoding,zh::SystemEncoding,$style['font']);
        $fontsize = empty($style['fontsize']) ?  12 : $style['fontsize'];
        $color    = empty($style['color']) ?  self::White : $style['color'];

        return imagettftext(
            $this->im,
            $fontsize,
            $angle,
            $x,$y,
            $color,
            $font,
            $utfstr   // 必需为utf-8字符串
        );

    }

    public function fill_background($backcolor){
        imagefilledrectangle($this->im,
                0,0,
                $this->get_width(),$this->get_height(),
                $backcolor);
    }


    //如果不给出文件名，则为输送到标准流中,默认以jpg格式
    public function savefile($type = 'jpg',$filename = NULL ){
        switch ($type){
            case 'jpg':
                if(empty($filename)){
                    header('Content-Type: image/jpeg');
                }
                imagejpeg($this->im,$filename);
                break;

            case 'gif':
                if(empty($filename)){
                    header('Content-Type: image/gif');
                }
                imagegif($this->im,$filename);
                break;

            case 'png':
                if(empty($filename)){
                    header('Content-Type: image/png');
                }
                imagepng($this->im,$filename);
                break;

            default:
                break;
        }
    }

    //--------------------------------------------------------------------------------
    // 静态功能函数


}
