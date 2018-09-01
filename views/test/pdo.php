<?php

$host = '127.0.0.1';    // 主机地址
$dbname = 'myblog';     // 数据库名
$user = 'root';         // 账号
$pass = '';             // 密码

// 连接数据库
$pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);

// 设置编码
$pdo->exec("SET NAMES utf8");


/*============================ exec =============================*/ 
// for($i=0;$i<100;$i++) {

//     $title = getChar(rand(20,50));
//     $content =  getChar(rand(100,500));
//     $pdo->exec("insert into blogs(title,content) values('$title','$content')"); 
// }

// function getChar($num)  // $num为生成汉字的数量
//     {
//         $b = '';
//         for ($i=0; $i<$num; $i++) {
//             // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
//             $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
//             // 转码
//             $b .= iconv('GB2312', 'UTF-8', $a);
//         }
//         return $b;
//     }

// 插入数据
// $pdo->exec("insert into blogs(id,title,content) values(1,'标题','内容')"); 


// 修改数据
// $pdo->exec("update blogs set title = '标题' where id = 1" );

// if($ret === false) {
//     die('出错了');
// }
// var_dump($ret);

// 删除数据
// $pdo->exec("delete from blogs where id = 3");


// 清空表，并重置 ID
// $pdo->exec("TRUNCATE blogs");





/*
    exec返回值：
        影响的行数
        如果SQL语句执行成功，返回影响的条数（可能返回0）
        如果SQL语句执行失败（SQL语句写错了），返回false
*/ 




/*========================= query =======================*/
// 取出前10条数据
// $stmt = $pdo->query('select * from blogs limit 10'); 

// 获取第一条记录，并返回一维数组
// $data = $stmt->fetch();

// 获取所有记录，返回 二维数组
// $data = $stmt->fetchAll();

// 获取一条记录，并返回对象
// $data = $stmt->fetch(PDO::FETCH_OBJ);
// echo $data->title;
// echo $data->content;

// 获取一条数据，并返回一维数组
// $data = $stmt->fetch(PDO::FETCH_ASSOC);
// echo $data['title'];
// echo $data['content'];


// 获取日志的数量
// $stmt = $pdo->query('select count(*) from blogs');

// $data = $stmt->fetch(PDO::FETCH_COLUMN);

// var_dump($data);


/*
    PDO::FETCH_ASSOC    返回关联数组
    PDO::FETCH_BOTH     返回混合数组
    PDO::FETCH_NUM      返回索引数组
    PDO::FETCH_OBJ      返回对象
    PDO::FETCH_COLUMN   返回某一列的值
*/ 


/*============================= 预处理 ==================*/

// $id = $_GET['id'];
// $title = $_GET['title'];
// // 第一种占位符 ？
// $sql = "delect from blogs where id = ? or title = ?";
// $stmt = $pdo->prepare($sql);
// $ret = $stmt->execute([
//     $id,
//     $title
// ]);

// if(!$ret) {
//     die('出错了');
// }

// 第二种占位符 可以使用任意字母，前面加上 :
// $sql = "delect from blogs where id = :blogid or title= :title";
// $stmt = $pdo->prepare($sql);
// $stmt->execute([
//     ':blogid' => $id,
//     ':title' => $title
// ]);


/*
    预处理：execute返回值
            true：1
            false：0
*/ 



// 预处理（案例）
$stmt = $pdo->prepare('insert into blogs(title,content) values(?,?)');
$ret = $stmt->execute([
    '标题xx',
    '内容xx'
]);

if($ret) {
    echo '添加成功新纪录的ID='. $pdo->lastInsertID();
} else {
    // 获取失败的原因
    $error = $stmt->errorInfo();
    echo '失败';
}

