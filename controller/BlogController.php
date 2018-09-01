<?php
namespace controller;

use PDO;

class BlogController {
    // 日志列表
    public function index() {

        // 取日志的数据
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=myblog",'root','');
        // 设置编码
        $pdo->exec('SET NAMES utf8');

        // 设置的 $where
        $where = 1;

        // 放预处理对应的值
        $value = [];

        // 如果有keyword 并且 值不为空时
        if(isset($_GET['keyword']) && $_GET['keyword']) {
            $where .= " AND (title LIKE ? OR content LIKE ?)";
            $value[] = '%'.$_GET['keyword'].'%';
            $value[] = '%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['start_date']) && $_GET['start_date']) {
            $where .= " AND created_at >= ?";
            $value[] = $_GET['start_date'];
        }

        if(isset($_GET['end_date']) && $_GET['end_date']) {
            $where .= " AND created_at <= ?";
            $value[] = $_GET['end_date'];
        }

        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0')) {
            $where .= " AND is_show = ?";
            $value[] = $_GET['is_show'];
        }

        /*====================== 排序 ==========================*/
        
        // 默认的排序条件
        $odby = 'created_at';
        $odway = 'desc';

        // 设置排序字段
        if(isset($_GET['odby']) && $_GET['odby'] == 'display') {
            $odby = 'display';
        }

        // 设置排序方式
        if(isset($_GET['odway']) && $_GET['odway'] == 'asc') {
            $odway = 'asc';
        }


        /* ========================= 翻页 ====================*/
        $perpage = 15;  // 每页15
        // 接收当前页码（大于等于1的整数），max: 是参数中大的值
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        
        $offset = ($page-1)*$perpage;

        // 制作按钮
        // 取出总的记录数
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
        $stmt->execute($value);
        $count = $stmt->fetch( PDO::FETCH_COLUMN );

        // 计算总的页数 (ceil: 向上取整（天花板），floor：向下取整（地板）)
        $pageCount = ceil( $count / $perpage );

        $btns = '';
        for($i=1; $i<=$pageCount; $i++) {
            // 先获取之前的参数
            $params = getUrlParams(['page']);

            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'> $i </a>";
            
        }


        /*================== 执行SQL ====================*/ 
        // 预处理 SQL
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage");
        
        // 执行 SQL
        $stmt->execute($value);

        // $error = $pdo->errorInfo();
        // echo '<pre>';
        // var_dump($error);

        // echo "select * from blogs where $where";die;

        // 取数据
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

        // 加载视图
        view('blogs.index', [
            'data' => $data,
            'btns' => $btns,
        ]);
    }
}