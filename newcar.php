<?php
/**
  * @file 可以改成任意扩展名为 .php 的文件
  * @author 吴星(maasdruck@gmail.com)
  * @date 2017/02/28
  * @version v1.01
  * @brief 抓取解析爱卡汽车口碑
  * @compatibility 支持 php 5.3 以上版本
  */

// 测试解析单一列表页
$c = curl_init();
curl_setopt($c, CURLOPT_HEADER, 0);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 16);
curl_setopt($c, CURLOPT_TIMEOUT, 16);
curl_setopt($c, CURLOPT_URL, 'http://newcar.xcar.com.cn/63/review/0.htm');
$newcar = curl_exec($c);
curl_close($c);

// 抓取过来的页面转换为 utf-8 中文编码
$newcar_u = iconv("GBK","UTF-8//IGNORE", $newcar);

// 全部点评开始
$newcar_1 = explode('全部点评</a></div>', $newcar_u);
// 全部点评结束
$newcar_2 = explode('<div class="pagers">', $newcar_1[1]);

// 测试解析后的内容
echo $newcar_2[0];
