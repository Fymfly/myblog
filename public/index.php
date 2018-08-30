<?php
// 定义常量     (根目录)
define('ROOT', dirname(__FILE__). '/../');

// 类的自动加载
function autoload($class) {
    // echo $class;
    $path = str_replace('\\', '/', $class);
    
    require(ROOT . $path . '.php');
}

spl_autoload_register('autoload');

// 添加路由：解析 URL 上的路径：控制器/方法
// 获取URL上的路径

if(isset($_SERVER['PATH_INFO'])) {

    $pathInfo = $_SERVER['PATH_INFO'];
    // 根据 / 转成数组
    $pathInfo = explode('/', $pathInfo);

    // 得到控制器名和方法名
    $controller = ucfirst($pathInfo[1]) . 'Controller';
    
    $action = $pathInfo[2];


    echo '<pre>';
    // var_dump($pathInfo);
    // die;

} else {
    // 默认控制器和方法
    $controller = 'IndexController';
    $action = 'index';
}

//为控制器添加命名空间
$fullController = 'controller\\'.$controller; 


echo '<pre>';   // 格式化数组
// var_dump($_SERVER);

// die;
// echo $fullController;
$_C = new $fullController;
$_C->$action();



// 加载视图
// 参数一、加载的视图的文件名
// 参数二、向视图中传的数据
function view($viewFileName, $data = []) {

    // 解压数组
    extract($data);

    $path = str_replace('.','/', $viewFileName). '.html' ;

    // 加载视图
    require(ROOT . 'views/' . $path);
    // echo ROOT . 'views/' . $path;
}