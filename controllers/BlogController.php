<?php
namespace controllers;

use models\Blog;

use PDO;

class BlogController {

    // 日志列表
    public function index() {

        $blog = new BLog;

        // 搜索日志
        $data = $blog->search();
        // 加载视图
        view('blogs.index',$data);
        
    }


    // 为所有的日志生成详情页（静态页）
    public function content_to_html() {   
        
        $blog = new Blog;
        $blog->content2html();
    }


    // 翻页
    public function index2html() {
        $blog = new Blog;
        $blog->index2html();
    }


    // 浏览量
    public function display() {
       
        // 接收日志ID
        $id = (int)$_GET['id'];

        $blog = new Blog;

        // 把浏览量+1，并输出（如果内存中没有就查询数据库，如果内存中有就直接操作内容） 
        echo $blog->getDisplay($id);
    }


    // 回写浏览量到数据库
    public function displayToDb() {
        $blog = new Blog;
        $blog->displayToDb();
    }

}