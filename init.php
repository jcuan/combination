<?php 

//使用到的常量
define ('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR.'app'); //配置成绝对路径
define ('BASE_PATH',__DIR__); //配置成绝对路径
define ('INDEX_PATH',__DIR__.DIRECTORY_SEPARATOR.'web');  //index.php所在的路径

define ('ENV','dev');   //或者是prod，会影响配置文件

mb_internal_encoding('utf-8');	//设置mb等的默认字符集
date_default_timezone_set('Asia/Shanghai');


require BASE_PATH . '/vendor/autoload.php';

//设置错误报告
switch (ENV)
{
	case 'dev':
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		break;

	case 'prod':
		error_reporting(E_ALL & ~E_DEPRECATED  & ~E_USER_DEPRECATED);
		ini_set('display_errors', 0);
		break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}
//错误处理函数

$app = Star\Core\App::getInstance();
$app->init();