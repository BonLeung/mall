<?php
/**
 * Created by PhpStorm.
 * User: liangweibang
 * Date: 2017/6/19
 * Time: 0:42
 */

include_once './lib/fun.php';

if(!checkLogin()) {
    msg(2, '请登录', 'login.php');
}

// 表单进行了提交处理
if (!empty($_POST['name'])) {
    $con = mysqlInit('localhost', 'root', '123', 'imooc_mall');

    if (!$goods_id = $_POST['id']) {
        msg(2, '参数非法');
    }

    // 根据商品 id 校验商品信息
    $sql = "SELECT * FROM `im_goods` WHERE `id` = {$goods_id}";
    $obj = mysql_query($sql);

    if (!$goods = mysql_fetch_assoc($obj)) {
        msg(2, '画品不存在', 'index.php');
    }

    // 处理表单数据
    // 画品名称
    $name = mysql_real_escape_string(trim($_POST['name']));
    // 画品价格
    $price = intval($_POST['price']);
    // 画品简介
    $desc = mysql_real_escape_string(trim($_POST['desc']));
    // 画品详情
    $content = mysql_real_escape_string(trim($_POST['content']));

    $name_length = mb_strlen($name, 'utf-8');
    if ($name_length <=0 || $name_length > 30) {
        msg(2, '商品名应在1-30字符之内');
    }

    if ($price <= 0 || $price > 999999999) {
        msg(2, '化品价格应少于 999999999');
    }

    $desc_length = mb_strlen($desc, 'utf-8');
    if ($desc_length <= 0 || $desc_length > 100) {
        msg(2, '商品简介应在1-100字符之内');
    }

    if (empty($content)) {
        msg(2, '画品详情不能为空');
    }

    // 更新数组
    $update = array(
        'name' => $name,
        'price' => $price,
        'desc' => $desc,
        'content' => $content
    );

    var_dump($goods);
    // 仅当用户选择上传图片，才进行图片上传处理
    if ($_FILES['file']['size'] > 0) {
        $pic = imageUpload($_FILES['file']);
        $update['pic'] = $pic;
    }

    // 只更新被更改的信息
    foreach ($update as $key => $val) {
        if ($goods[$key] == $val) {
            unset($update[$key]);
        }
    }

    // 对比两个数组，如果没有需要更新的字段
    if (empty($update)) {
        msg(1, "操作成功", 'edit.php?id='.$goods_id);
    }

    var_dump($update);

    // 更新 sql 处理
    $update_sql = '';
    foreach($update as $key => $val) {
        $update_sql .= "`{$key}` = '{$val}',";
    }
    // 去除多余 ,
    $update_sql = rtrim($update_sql, ',');

    unset($sql, $obj, $result);

    $sql = "UPDATE `im_goods` SET {$update_sql} WHERE `id` = {$goods_id}";

//    echo $sql; die;

    // 当更新成功
    if ($result = mysql_query($sql)) {
        msg(1, '操作成功', 'edit.php?id=' . $goods_id);
    } else {
        msg(2, '操作失败', 'edit.php?id=' . $goods_id);
    }

} else {
    msg(2, '路由非法', 'index.php');
}

