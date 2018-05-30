<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"E:\phpStudy\WWW\yuqingyong\public/../application/home\view\Index\login.html";i:1516915804;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" name="viewport" />
<meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" name="viewport">
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta content="telephone=no" name="format-detection" />
<title>用户登录</title>
<style>
body{ margin:0; padding:0; color:#666; background:#c1d2fe;}
.form_login{ max-width:640px; margin:auto; text-align:center; padding-top:100px;}
.form-group{ width:355px; margin:0 auto; height:50px; margin-bottom:20px;}
.form-group .fa{ display:block; width:50px; height:50px; float:left;}
.form-group .form-control{ display:block; width:300px; height:48px; float:left; border:1px solid #ccc; padding:0; margin-left:0; text-indent:1em; themeColor: #00a988;}
.form-group .form-control:hover{ border:1px solid #0160A0;}
.form-group .checkfont{ color:#666;}
.form-group .btn{ width:350px; height:50px; background-color:#0160A0; border:0px; color:#fff; font-size:14px;}
.fa-user{ background:url(/static/home/images/user.png) no-repeat center;}
.fa-key{ background:url(/static/home/images/pw.png) no-repeat center;}
.form_footer{ margin-top:100px; font-size:12px; color:#5B809A;}
#register{text-align: right;}
#register a{text-decoration: none;color:#666 ;}
.index{text-align: right;margin-top: 10px;margin-right: 20px;}
.index a{text-decoration: none;color: #0160A0;display: block;}
</style>
</head>

<body>
<div class="index"><a href="<?php echo url('home/Index/index'); ?>">返回首页>></a></div>
<div class="form_login">
<div class="form_logo"><img src="__PUBLIC__/home/images/logo.png" /></div>
<form method="post" role="form" action="<?php echo url('home/Index/login'); ?>" id="form_login">

    <div class="form-group">
    	<i class="fa fa-user"></i>
		<input type="text" class="form-control" name="username" id="username" placeholder="账号" autocomplete="off" value="<?php echo \think\Cookie::get('username'); ?>">
    </div>

    <div class="form-group">
		<i class="fa fa-key"></i>
        <input type="password" class="form-control" name="password" id="password" placeholder="密码" autocomplete="off" value="<?php echo \think\Cookie::get('password'); ?>">
    </div>
    
    <div class="form-group">
		<img src="<?php echo captcha_src(); ?>" alt="captcha" class="fl" style="width:155px;height: 45px;" onclick="this.src='<?php echo captcha_src(); ?>?seed='+Math.random()">
		<input type="text" size="8" name="code" class="form-control text fl" style="width:155px;height: 43px;" placeholder="验证码">
	</div>
    
    <div class="form-group" style="height:25px; line-height:25px; text-align:left;">
    	<?php if(\think\Cookie::get('remember') == 1): ?>
            <input type="checkbox" id="checkbox" name="remember" value="1" checked="checked" />
	        <span class="checkfont">记住我的帐号</span>
	    <?php else: ?> 
            <input type="checkbox" id="checkbox" name="remember" value="1" />
            <span class="checkfont">记住我的帐号</span>
	    <?php endif; ?>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block btn-login">登录</button>
        <p id="register"><a href="<?php echo url('home/Index/register'); ?>">没有账号，去注册！</a></p>
    </div>

</form>
<div class="form_footer">@2017 星辰网络博客</div>
</div>
</body>
</html>
