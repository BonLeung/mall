<?php
    include_once './lib/fun.php';

    // 开启 session
    session_start();
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
        header('Location: index.php');
    }
    // 表单进行了提交处理
    if(!empty($_POST['username'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!$username) {
            echo '用户名不能为空';
            msg(2, '用户名不能为空', 'login.php');
            exit;
        }
        if (!$password) {
            msg(2, '密码不能为空', 'login.php');
            echo '密码不能为空';
            exit;
        }

        // 获取数据库连接
        $con = mysqlInit('localhost', 'root', '123', 'imooc_mall');
        if (!$con) {
            echo mysql_error();
            exit;
        }

        // 根据用户名查询用户
        $sql = "SELECT * FROM im_user WHERE username = '{$username}'";
        $obj = mysql_query($sql);
        $result = mysql_fetch_assoc($obj);

        if (is_array($result) && !empty($result)) {
            if (createPassword($password) === $result['password']) {
                $_SESSION['user'] = $result;
               header('Location:index.php'); exit;
            } else {
                msg(2, '密码不正确，请重新输入', 'login.php'); exit;
            }
        } else {
            msg(2, '用户名不存在，请重新输入', 'login.php'); exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|用户登录</title>
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
                        <p>用户登录</p>
                    </div>
                    <form class="login-table" name="login" id="login-form" action="login.php" method="post">
                        <div class="login-left">
                            <label class="username">用户名</label>
                            <input type="text" class="yhmiput" name="username" placeholder="Username" id="username">
                        </div>
                        <div class="login-right">
                            <label class="passwd">密码</label>
                            <input type="password" class="yhmiput" name="password" placeholder="Password" id="password">
                        </div>
                        <div class="login-btn">
                            <button type="submit">登录</button>
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
        $('#login-form').submit(function () {
            var username = $('#username').val(),
                password = $('#password').val();
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
            return true;
        })
    })
</script>
</html >