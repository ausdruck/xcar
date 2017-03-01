<?php
/**
  * @file 可以改成任意扩展名为 .php 的文件
  * @author 吴星(maasdruck@gmail.com)
  * @date 2017/03/01
  * @version v1.02
  * @brief 抓取解析爱卡汽车口碑
  * @compatibility 支持 php 5.3 以上版本
  */

$sql_create_p = @$_GET['sql_create_p'];
$sql_delete_p = @$_GET['sql_delete_p'];
$sql_write_p = @$_GET['sql_write_p'];

echo '
<div>
    <form method="get" action="newcar.php">';
    echo '
        <span>
            <input type="checkbox" name="sql_create_p';
    if (@$_GET['sql_create_p'] == 'on') {
        echo  '" checked="checked" value="on';
    }
    echo '">创建表格
            <input type="checkbox" name="sql_delete_p';
    if (@$_GET['sql_delete_p'] == 'on') {
        echo  '" checked="checked" value="on';
    }
    echo '">删除表格
            <input type="checkbox" name="sql_write_p';
    if (@$_GET['sql_write_p'] == 'on') {
        echo  '" checked="checked" value="on';
    }
    echo '">写入数据
        </span>
        <input type="submit" value="执行">
    </form>
</div>
';

// 初始化数据库
$con = mysqli_init();

if(!$con) {
    echo 'mysqli_init error';
    exit(0);
}

// 登录信息
$switch = 0;
if ($switch == 0) {
    $ret = mysqli_real_connect($con, 'localhost', 'root', 'asdf', 'crawl', 3306);
}

if(!$ret) {
    echo 'mysqli_real_connect error';
    exit(0);
}

// 中文编码，保证传进 mysql 中文不出现乱码
$con -> set_charset('utf8');

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 创建表格
if ($sql_create_p == 'on') {
    $sql = "create table xcar_newcar (
        id int not null primary key auto_increment,
        na_1s char(2) not null,
        na_2s char(2) not null,
        na_3s varchar(63) not null,
        na_4s varchar(31) not null,
        na_5s varchar(63) not null,
        na_6s varchar(31) not null,
        update_time datetime not null
    )";
    $ret = mysqli_query($con, $sql);
    if(!$ret) {
        echo 'mysqli_query error';
        exit(0);
    }
}

// 删除表格
if ($sql_delete_p == 'on') {
    $sql = "drop table xcar_newcar";
    $ret = mysqli_query($con, $sql);
    if(!$ret) {
        echo 'mysqli_query error';
        exit(0);
    }
}

$c = curl_init();
curl_setopt($c, CURLOPT_HEADER, 0);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 16);
curl_setopt($c, CURLOPT_TIMEOUT, 16);
curl_setopt($c, CURLOPT_URL, 'http://newcar.xcar.com.cn/63/review/0.htm');
$newcar = curl_exec($c);
curl_close($c);

$newcar_u = iconv("GBK","UTF-8//IGNORE", $newcar);

$newcar_1 = explode('全部点评</a></div>', $newcar_u);
$newcar_2 = explode('<div class="pagers">', $newcar_1[1]);
$newcar_3s = explode('<dl><dt><em>点评角度：', $newcar_2[0]);

array_shift($newcar_3s);

foreach ($newcar_3s as $newcar_3sk) {
    $newcar_4s = explode('</a></em>', $newcar_3sk);
    $na_1s = substr($newcar_4s[0], -6);
    echo '<hr>'.$na_1s;
    $newcar_5s = explode('】<a href="http://www.xcar.com.cn/', $newcar_4s[1]);
    $na_2s = substr($newcar_5s[0], 3);
    echo '&nbsp;'.$na_2s;
    $newcar_6s = explode('" target="_blank">', $newcar_5s[1]);
    $na_3s = $newcar_6s[0];
    echo '&nbsp;'.$na_3s;
    $newcar_7s = explode('</a></dt>', $newcar_6s[1]);
    $newcar_8s = explode(' ', $newcar_7s[0]);
    $na_4s = $newcar_8s[0];
    echo '&nbsp;'.$na_4s;
    $na_5s = $newcar_8s[1];
    echo '&nbsp;'.$na_5s;
    $newcar_9s = explode('</a> 作者：', $newcar_6s[2]);
    $na_6s = $newcar_9s[0];
    echo '&nbsp;'.$na_6s;
    echo '<hr>'.$newcar_9s[1];
    echo '<hr>'.$newcar_7s[1];
    $update_time = date('Y-m-d H:i:s', time());
    $sql = "insert into xcar_newcar (
            na_1s,
            na_2s,
            na_3s,
            na_4s,
            na_5s,
            na_6s,
            update_time
        )
        values (
            '{$na_1s}',
            '{$na_2s}',
            '{$na_3s}',
            '{$na_4s}',
            '{$na_5s}',
            '{$na_6s}',
            '{$update_time}'
        )";
    if ($sql_write_p == 'on') {
        $ret = mysqli_query($con, $sql);
        if(!$ret) {
            echo 'mysqli_query error';
            exit(0);
        }
    }
}
