<?php
/**
 * Created by PhpStorm.
 * User: liangweibang
 * Date: 2017/6/18
 * Time: 16:56
 */

/**
 * 数据库函数初始化
 * @param $host
 * @param $username
 * @param $password
 * @param $dbname
 * @return bool|resource
 */
function mysqlInit($host, $username, $password, $db_name) {
    $con = mysql_connect($host, $username, $password);
    if (!$con) {
        return false;
    }

    mysql_select_db($db_name);
    // 设置字符集
    mysql_set_charset('utf8');

    return $con;
}

function createPassword($password) {
    if (!$password) {
        return false;
    }

    return md5(md5($password).'IMOOC');
}

/**
 * 消息提示
 * @param $type 1：成功 2：失败
 * @param null $msg
 * @param null $url
 */
function msg($type, $msg = null, $url = null) {
    $toUrl = "Location:msg.php?type={$type}";

    $toUrl .= $msg ? "&msg={$msg}" : '';

    $toUrl .= $url ? "&url={$url}" : '';

    header($toUrl); exit;
}

function imageUpload($file) {

    // 检查上传文件是否合法
    if (!is_uploaded_file($file['tmp_name'])) {
        msg(2, '请上传符合规范的图像');
    }

    // 图像类型验证
    $type = $file['type'];

    if (!in_array($type, array("image/png", "image/gif", "image/jpeg"))) {
        msg(2, '请上传 png, gif, jpeg 格式的图像');
    }


    // 上传目录
    $upload_path = './static/file/';
    // 上传目录访问 Url
    $upload_url = '/static/file/';
    // 上传文件夹
    $file_dir = date('Y/md/', $_SERVER['REQUEST_TIME']);

    if (!is_dir($upload_path.$file_dir)) {
        mkdir($upload_path . $file_dir, 0755, true);  // 递归创建目录
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // 上传图像名称
    $img = uniqid() . mt_rand(1000, 9999) . '.' . $ext;

    // 物理地址
    $img_path = $upload_path . $file_dir . $img;
    // url地址
    $img_url = 'http://localhost/mall' . $upload_url . $file_dir . $img;

    var_dump($img_url, $img_path);

    if (!move_uploaded_file($file['tmp_name'], $img_path)) {
        msg(2, '服务器繁忙，请稍后再试');
    }

    return $img_url;
}

/**
 * 检查用户登录
 * @return bool
 */
function checkLogin() {
    // 开启session
    session_start();
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        return false;
    }
    return true;
}

/**
 * 获取当前 url
 * @return string
 */
function getUrl() {
    $url = '';
    $url .= $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];

    return $url;
}

/**
 * 根据 page 生成 url
 * @param $page
 * @param string $url
 * @return string
 */
function pageUrl($page, $url = '') {
    $url = empty($url) ? getUrl() : $url;

    // 查询 url 中是否存在问号 ？
    $pos = strpos($url, '?');
    if ($pos === false) {
        $url .= "?page={$page}";
    } else {
        $query_str = substr($url, $pos + 1);
        // 解析 query_str  为数组
        parse_str($query_str, $query_arr);
        if (isset($query_arr['page'])) {
            unset($query_arr['page']);
        }
        $query_arr['page'] = $page;

        // 将 query_arr 重新拼装成 query_str
        $query_str = http_build_query($query_arr);

        $url = substr($url, 0, $pos) . '?' . $query_str;
        var_dump($url);
    }

    return $url;

}

/**
 * 分页显示
 * @param int $total 数据总数
 * @param int $current_page 当前页
 * @param int $page_size 每页显示条数
 * @param int $show 显示按钮数
 * @return string
 */
function pages($total, $current_page, $page_size, $show = 6) {
    $page_str = '';

    // 仅当总数大于每页显示条数，才进行分页处理
    if ($total > $page_size) {
        // 总页数
        $total_page = ceil($total / $page_size); // 向上取整 获取总页数

        // 对当前页进行处理
        $current_page = $current_page > $total_page ? $total_page : $current_page;
        // 分页起始页
        $from = max(1, ($current_page - intval($show / 2)));
        // 分页结束页
        $to = $from + $show - 1;

        // 当结束页大于总页
        if ($to > $total_page) {
            $to = $total_page;
            $from = max(1, $to - $show + 1);
        }

        $page_str = '<div class="page-nav">';
        $page_str .= '<ul>';

        // 仅当当前页大于 1 的时候，存在首页和上一页的按钮
        if ($current_page > 1) {
            $page_str .= "<li><a href='" . pageUrl(1) . "'>首页</a></li>";
            $page_str .= "<li><a href='" . pageUrl(($current_page - 1)) . "'>上一页</a></li>";
        }

        if ($from > 1) {
            $page_str .= '<li>...</li>';
        }

        for ($i = $from; $i <= $to; $i++) {
            if ($i != $current_page) {
                $page_str .= "<li><a href='" . pageUrl($i) . "'>{$i}</a></li>";
            } else {
                $page_str .= "<li><span class='curr-page'>{$i}</span></li>";
            }
        }

        if ($to < $total_page) {
            $page_str .= '<li>...</li>';
        }

        var_dump($total_page);
        var_dump($current_page);

        if ($current_page < $total_page) {
            $page_str .= "<li><a href='" . pageUrl($current_page + 1) . "'>下一页</a></li>";
            $page_str .= "<li><a href='" . pageUrl($total_page) . "'>尾页</a></li>";
        }

        $page_str .= '</ul>';
        $page_str .= '</div>';

    }

    return $page_str;
}

