<?php
/**
* TestGuest Version1.0
* ================================================
* Copy 2016-2016 
* Web: http://www..com
* ================================================
* Author: word
* Date: 2016年9月21日
*/
session_start();
/*判断是否是非法调用*/
define('IN_TG', true);
/*定义常量证明这是注册*/
define('SCRIPT', 'friend');
//引用公共信息
require dirname(__FILE__).'/includes/common.inc.php';

//判断是否登录
if(!isset($_COOKIE['username'])){
    _alert_close('请先登录！');
}
//加好友
if($_GET['action']=='add'){
    //为防止恶意注册，跨站攻击
    _check_yzm($_POST['yzm'], $_SESSION['code']);
    if(!!$_rows=_fetch_array("SELECT tg_uniqid FROM tg_user WHERE tg_username='{$_COOKIE['username']}' LIMIT 1")){
        _uniqid($_rows['tg_uniqid'], $_COOKIE['uniqid']);
        //引入验证文件
        include ROOT_PATH.'includes/check.func.php';
        $_clean=array();
        $_clean['touser']=$_POST['touser'];
        $_clean['fromuser']=$_COOKIE['username'];
        $_clean['content']=_check_content($_POST['content']);
        $_clean=_mysql_string($_clean);
        //判断是否是添加自己
        if($_clean['touser']==$_clean['fromuser']){
            _alert_close('请不要添加自己！');
        }
        //数据库验证好友是否已经添加
        if(!!$_rows=_fetch_array("SELECT tg_id FROM tg_friend WHERE tg_touser='{$_clean['touser']}' AND tg_fromuser='{$_clean['fromuser']}' OR tg_touser='{$_clean['fromuser']}' AND tg_fromuser='{$_clean['touser']}' LIMIT 1")){
            _alert_close('你们已经是好友了，或者未验证的好友，无需再添加！');
        }else {
            //添加好友信息
        _query("INSERT INTO tg_friend(
                                        tg_touser,
                                        tg_fromuser,
                                        tg_content,
                                        tg_date
                                        )
                                   VALUES(
                                            '{$_clean['touser']}',
                                            '{$_clean['fromuser']}',
                                            '{$_clean['content']}',
                                            NOW()
                                          )
        ");
        if (_affected_rows()==1){
            //关闭数据库清空session
            mysql_close();
           // session_destroy();
            _alert_close("好友添加成功，请等待验证！");
        
        }else {
            //关闭数据库
            mysql_close();
           // session_destroy();
            //弹出提示后跳转到首页
            _alert_back("添加失败！");
        }
        }
    }else{
        _alert_close('非法登录！');
    }
}
//获取数据
if(isset($_GET['id'])){
   if (!!$_rows=_fetch_array("SELECT tg_username FROM tg_user WHERE tg_id='{$_GET['id']}' LIMIT 1")){
       $_html=array();
       $_html['touser']=$_rows['tg_username'];
       $_html=_html($_html);
   }else{
       _alert_close('不存在此用户！');
   }
}else{
    _alert_close('非法操作！');
}





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php 
    //公共头文件
    require ROOT_PATH.'includes/title.inc.php';
?>
<script type="text/javascript" src="js/code.js"></script>
<script type="text/javascript" src="js/message.js"></script>
</head>
<body>

<div id="message">
    <h3>加好友</h3>
    <form method="post" action="?action=add">
    <input type="hidden" class="text" name="touser" value="<?php echo $_html['touser'];?>"/>
        <dl>
            <dd><input type="text" class="text" readonly="readonly" value="<?php echo 'To:'.$_html['touser'];?>"/></dd>
            <dd><textarea name="content">我很想和你交朋友，很想和你交朋友！</textarea></dd>
            <dd><span>验证码:</span><input type="text" name="yzm" class="text yzm"/><img src='
            code.php'id="code"/><input type="submit" name="button" class="submit" value="添加好友"/></dd>
         
        </dl>
    </form>
</div>
</body>
</html>