<?php

// 创建phar包的辅助脚本
// 使用方式：
//     将本脚本放置到要打包的目录下，修改对应的名字运行即可

$file = 'Sample.phar';            // 包的名称,在stub中也作为入口前缀
$exts = ['php', 'html','inc'];    // 需要打包的文件后缀
$dir  = __DIR__;                   // 需要打包的目录

$phar = new Phar(__DIR__ . '/' . $file,
          FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
          $file);

// 开始打包
$phar->startBuffering();
// 将后缀名相关的文件打包
foreach ($exts as $ext) {
    $phar->buildFromDirectory($dir, '/\.' . $ext . '$/');
}
// 把build.php本身摘除
$phar->delete('build.php');
// 设置入口
$phar->setStub("<?php
Phar::mapPhar('{$file}');
require 'phar://{$file}/portal/index.php';
__HALT_COMPILER();
?>");
$phar->stopBuffering();
// 打包完成
echo "Finished {$file}\n";
