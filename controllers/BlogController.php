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
    public function content_to_html()
    {   
        
        $blog = new Blog;
        $a = $blog->content2html();
        echo $a;
    }


    // 翻页
    public function index2html() {
        $blog = new Blog;
        $blog->index2html();
    }


    // 浏览量
    public function updata_display() {
        // 接收日志ID
        $id = (int)$_GET['id'];

        // 连接 Redis
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 32768,
        ]);

        // 判断 blog_displays 这个 hash 中有没有一个键是 blog-$id 

        $key = "blog-{$id}";   // 拼出日志的键

        // 判断 hash 中是否有这个值
        if($redis->hexists('blog_displays', $key))
        {
            // 累加 并且 返回添加完之后的值
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            echo $newNum;
        }
        else
        {
            // 从数据库中取出浏览量
            $blog = new Blog;
            $display = $blog->getDisplay($id);
            $display++;
            // 加到 redis
            $redis->hset('blog_displays', $key, $display);
            echo $display;
        }
    }

}