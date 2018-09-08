<?php
namespace controllers;
use models\Blog;

class BlogController{

    // 更新表单
    public function update() {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];

        // 更新数据库
        $blog = new Blog;
        $blog->update($title, $content, $is_show, $id);

        // 如果日志是公开的就生成静态页
        if($is_show == 1) {

            $blog->makeHtml($id);
        }
        else {
            // 如果改为私有，就要将原来的静态页删除掉
            $blog->deleteHtml($id);
        }

        message('修改成功！', 0, '/blog/index');
    }


    // 修改表单
    public function edit() {
        $id = $_GET['id'];
        // 根据ID取出日志的信息

        $blog = new Blog;
        $data = $blog->find($id);

        view('blogs.edit', [
            'data' => $data,
        ]);
    }


    // 删除
    public function delete() {
        $id = $_POST['id'];

        $blog = new Blog;
        $blog->delete($id);

        // 把静态页删除
        $blog->deleteHtml($id);

        message('删除成功',2,'/blog/index');
    }

    //日志列表
    public function index(){

        $blog = new BLog;
        // 搜索数据
        $data = $blog->search();

        view('blogs.index',$data);
       
    }


    //为日志生成详情页
    public function content_to_html(){
        $blog = new Blog;
        $blog->content2html();
    }


    public function index2html(){
        $blog = new Blog;
        $blog->index2html();
    }


    public function display()
    {
        //接收日志id
        $id = (int)$_GET['id'];
        // echo $id;
        // echo" <br/>";
        $blog = new Blog;
        //把浏览量+1 并输出 （如果内存中没有就查询数据库，如果内存有就直接操作）
        echo $blog->getDisplay($id);

    }


    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }

    // 显示添加日志的表单
    public function create(){
       view('blogs.create');
    } 


    // 添加日志
    public function store(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        // 添加新日志并返回 新日志的ID
        $id = $blog->add($title,$content,$is_show);

        // 如果日志是公开的就生成静态页
        if($is_show == 1) {
            $blog->makeHtml($id);
        }

        //跳转
        message('发表成功',2,'/blog/index');
    }
  
 }
    
