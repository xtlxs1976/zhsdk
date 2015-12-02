<?php

namespace zh;

interface zh{
    const Version = 1.0;

    const SystemEncoding = 'GBK';     // 当前操作系统编码，中文windows下常为GBK，linux下一般为 UTF-8
    const ScriptEncoding = 'UTF-8';   // 脚本文件的编码，一般用UTF-8
    const HtmlEncoding = 'UTF-8';     // 页面的默认编码设置

    const TimeZone = 'Asia/Shanghai';  // 设置时区

}
