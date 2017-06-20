<?php
/**
 * Created by PhpStorm.
 * User: liangweibang
 * Date: 2017/6/19
 * Time: 22:35
 */
include_once './lib/fun.php';

session_start();
// 释放 user
unset($_SESSION['user']);
msg(1, '退出登录成功', 'index.php');