<?php
    include_once './lib/fun.php';

  if (!empty($_POST['username'])) {
      $username = trim($_POST['username']);
      $password = trim($_POST['password']);
      $repassword = trim($_POST['repassword']);

      // 判断用户名不能为空
      if (!$username) {
          msg(2, '用户名不能为空', 'register.php'); exit;
      }
      if (!$password) {
          msg(2, '密码不能为空', 'register.php'); exit;
      }
      if (!$repassword) {
          msg(2, '确认密码不能为空', 'register.php'); exit;
      }
      if ($password != $repassword) {
          msg(2, '两次输入的密码不一致', 'register.php'); exit;
      }

      // 数据库操作
      // 获取数据库连接
      $con = mysqlInit('localhost', 'root', '123', 'imooc_mall');
      if (!$con) {
          echo mysql_error();
          exit;
      }

      // 判断用户是否在数据表中存在
      $sql = "SELECT COUNT(`id`) as total FROM `im_user` WHERE `username` = '{$username}'";

      $obj = mysql_query($sql);

      $result = mysql_fetch_assoc($obj);

      // 用户已存在数据库
      if (isset($result['total']) && $result['total'] > 0) {
          msg(2, '用户名已存在，请重新输入', 'register.php');
          exit;
      }

      // 密码加密处理
      $password = createPassword($password);
      // 插入数据
      $sql = "INSERT INTO im_user(username, password, create_time) VALUES('{$username}', '{$password}', '{$_SERVER['REQUEST_TIME']}')";

      unset($obj, $result);

      $obj = mysql_query($sql);
        if ($obj) {
            msg(1, '恭喜您注册成功', 'index.php');
        } else {
            msg(2, '注册失败', 'register.php');
            exit;
        }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|用户注册</title>
    <link type="text/css" rel="stylesheet" href="./static/css/common.css">
    <link type="text/css" rel="stylesheet" href="./static/css/add.css">
    <link rel="stylesheet" type="text/css" href="./static/css/login.css">
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="center">
        <div class="center-login">
            <div class="login-banner">
                <a href="#"><img src="./static/image/login_banner.png" alt=""></a>
            </div>
            <div class="user-login">
                <div class="user-box">
                    <div class="user-title">
                        <p>用户注册</p>
                    </div>
                    <form class="login-table" name="register" id="register-form" action="register.php" method="post">
                        <div class="login-left">
                            <label class="username">用户名</label>
                            <input type="text" class="yhmiput" name="username" placeholder="Username" id="username">
                        </div>
                        <div class="login-right">
                            <label class="passwd">密码</label>
                            <input type="password" class="yhmiput" name="password" placeholder="Password" id="password">
                        </div>
                        <div class="login-right">
                            <label class="passwd">确认</label>
                            <input type="password" class="yhmiput" name="repassword" placeholder="Repassword"
                                   id="repassword">
                        </div>
                        <div class="login-btn">
                            <button type="submit">注册</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <p><span>M-GALLARY</span> ©2017 POWERED BY IMOOC.INC</p>
</div>

</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script src="./static/js/layer/layer.js"></script>
<script>
    $(function () {
        $('#register-form').submit(function () {
            var username = $('#username').val(),
                password = $('#password').val(),
                repassword = $('#repassword').val();
            if (username == '' || username.length <= 0) {
                layer.tips('用户名不能为空', '#username', {time: 2000, tips: 2});
                $('#username').focus();
                return false;
            }

            if (password == '' || password.length <= 0) {
                layer.tips('密码不能为空', '#password', {time: 2000, tips: 2});
                $('#password').focus();
                return false;
            }

            if (repassword == '' || repassword.length <= 0 || (password != repassword)) {
                layer.tips('两次密码输入不一致', '#repassword', {time: 2000, tips: 2});
                $('#repassword').focus();
                return false;
            }

            return true;
        })

    })
</script>
</html>


