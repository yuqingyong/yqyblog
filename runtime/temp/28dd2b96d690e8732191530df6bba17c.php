<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Index\index.html";i:1530496896;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>星辰博客管理后台</title>
    <meta name="keywords" content="星辰博客,喻庆勇,网站后台,后台管理,管理系统,网站模板" />
    <meta name="description" content="星辰博客" /> 
    <link rel="stylesheet" href="__PUBLIC__/admin/css/pintuer.css">
    <link rel="stylesheet" href="__PUBLIC__/admin/css/admin.css">
    <script src="__PUBLIC__/admin/js/jquery.js"></script>   
</head>
<body style="background-color:#f2f9fd;">
<div class="header bg-main">
  <div class="logo margin-big-left fadein-top">
    <h1><img src="__PUBLIC__/admin/images/y.jpg" class="radius-circle rotate-hover" height="50" alt="" />后台管理中心</h1>
  </div>
  <div class="head-l"><a class="button button-little bg-green" href="<?php echo url('home/Index/index'); ?>" target="_blank"><span class="icon-home"></span> 前台首页</a>&nbsp;&nbsp;<a class="button button-little bg-red" href="<?php echo url('admin/Login/login_out'); ?>"><span class="icon-power-off"></span> 退出登录</a>&nbsp;&nbsp;<a class="button button-little bg-blue" href="<?php echo url('admin/Index/clear_cache'); ?>"><span class="icon-power-off"></span> 清除缓存</a></div>
</div>
<div class="leftnav">
  <div class="leftnav-title"><strong><span class="icon-list"></span>菜单列表</strong></div>
  <h2><span class="icon-user"></span>基本设置</h2>
  <ul style="display:block">
    <!-- <li><a href="<?php echo url('admin/Index/websit'); ?>" target="right"><span class="icon-caret-right"></span>网站设置</a></li> -->
    <li><a href="<?php echo url('admin/Login/edit_pass'); ?>" target="right"><span class="icon-caret-right"></span>修改密码</a></li>
    
  </ul>   
  <h2><span class="icon-pencil-square-o"></span>栏目管理</h2>
  <ul>
    <li><a href="<?php echo url('admin/User/user_list'); ?>" target="right"><span class="icon-caret-right"></span>会员管理</a></li>
    <li><a href="<?php echo url('admin/Article/article_list'); ?>" target="right"><span class="icon-caret-right"></span>文章列表</a></li>
    <li><a href="<?php echo url('admin/Chat/chat_list'); ?>" target="right"><span class="icon-caret-right"></span>闲言碎语</a></li>
    <li><a href="<?php echo url('admin/Comment/comment_list'); ?>" target="right"><span class="icon-caret-right"></span>评论列表</a></li>      
    <li><a href="<?php echo url('admin/Demand/demand_list'); ?>" target="right"><span class="icon-caret-right"></span>需求列表</a></li>   
    <li><a href="<?php echo url('admin/Message/message'); ?>" target="right"><span class="icon-caret-right"></span>留言列表</a></li>    
  </ul>
  <h2><span class="icon-pencil-square-o"></span>分类管理</h2>
  <ul>
    <li><a href="<?php echo url('admin/Categorys/category_list'); ?>" target="right"><span class="icon-caret-right"></span>文章分类</a></li>
    <li><a href="<?php echo url('admin/Categorys/tag_list'); ?>" target="right"><span class="icon-caret-right"></span>标签管理</a></li>
  </ul>
  <h2><span class="icon-pencil-square-o"></span>配置管理</h2>
  <ul>
    <li><a href="<?php echo url('admin/Config/config_list'); ?>" target="right"><span class="icon-caret-right"></span>配置列表</a></li>
  </ul>
  <h2><span class="icon-pencil-square-o"></span>其他管理</h2>
  <ul>
    <li><a href="<?php echo url('admin/Elses/img_list'); ?>" target="right"><span class="icon-caret-right"></span>图片列表</a></li>
    <li><a href="<?php echo url('admin/Elses/friend_url'); ?>" target="right"><span class="icon-caret-right"></span>友情链接</a></li>
    <li><a href="<?php echo url('admin/Article/huishou'); ?>" target="right"><span class="icon-caret-right"></span>文章回收站</a></li>
  </ul>
</div>
<script type="text/javascript">
$(function(){
  $(".leftnav h2").click(function(){
	  $(this).next().slideToggle(200);	
	  $(this).toggleClass("on"); 
  })
  $(".leftnav ul li a").click(function(){
	    $("#a_leader_txt").text($(this).text());
  		$(".leftnav ul li a").removeClass("on");
		$(this).addClass("on");
  })
});
</script>
<ul class="bread">
  <li><a href="" target="right" class="icon-home"> 首页</a></li>
  <li><a href="##" id="a_leader_txt">网站信息</a></li>
  <li><b>当前语言：</b><span style="color:red;">中文</php></span>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;切换语言：<a href="##">中文</a> &nbsp;&nbsp;<a href="##">英文</a> </li>
</ul>
<div class="admin">
  <iframe scrolling="auto" rameborder="0" src="<?php echo url('admin/Index/websit'); ?>" name="right" width="100%" height="100%"></iframe>
</div>
</body>
</html>