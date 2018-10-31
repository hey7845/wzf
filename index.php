<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//下面是一个完整的使用目录安全写入的例子
//define('BUILD_DIR_SECURE',true);
//define('DIR_SECURE_FILENAME', 'default.html');
//define('DIR_SECURE_CONTENT', 'deney Access!');
define('PATH',__DIR__);
// // 定义ThinkPHP框架路径
// define('THINK_PATH', PATH.'/../ThinkPHP/');
// //定义项目名称和路径
// define('APP_NAME', 'Vip');
// define('APP_PATH', PATH.'/../Vip/');
// 定义ThinkPHP框架路径
define('THINK_PATH', './ThinkPHP/');
//定义项目名称和路径
define('APP_NAME', './Vip');
define('APP_PATH', './Vip/');

define('APP_DEBUG',true);

// 加载框架入口文件
require(THINK_PATH."./ThinkPHP.php");

?>
