<?php
/**
 * Created by PhpStorm.
 * User: liangweibang
 * Date: 2017/6/19
 * Time: 22:05
 */

include_once './lib/fun.php';

if (!checkLogin()) {
    msg(2, '请登录', 'login.php');
}

$goods_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : '';

// 如果 id 不存在，跳转到商品列表
if (!$goods_id) {
    msg(2, '参数非法', 'index.php');
}

// 根据 id 查询商品信息
$con = mysqlInit('localhost', 'root', '123', 'imooc_mall');

$sql = "SELECT * FROM `im_goods` WHERE `id` = {$goods_id}";
$obj = mysql_query($sql);

// 当根据 id 查询商品信息为空，跳转到商品列表页
if (!$goods = mysql_fetch_assoc($obj)) {
    msg(2, '商品不存在', 'index.php');
}

// 删除处理
$sql = "DELETE FROM `im_goods` WHERE `id` = {$goods_id} LIMIT 1";

if ($result = mysql_query($sql)) {
//    mysql_affected_rows();
    msg(1, '操作成功', 'index.php');
    exit;
} else {
    msg(2, '操作失败', 'index.php');
    exit;
}

// 注意项
// 1. 项目中，不会真正删除商品，而是更新商品的 status 1：正常操作 -1：删除操作
// 2. 增加商品编辑时 update_time


