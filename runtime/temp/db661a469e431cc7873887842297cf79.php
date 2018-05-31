<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Index\websit.html";i:1516915804;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>网站信息</title>  
    <link rel="stylesheet" href="__PUBLIC__/admin/css/pintuer.css">
    <link rel="stylesheet" href="__PUBLIC__/admin/css/admin.css">
    <script src="__PUBLIC__/admin/js/jquery.js"></script>
    <script src="__PUBLIC__/admin/js/pintuer.js"></script>  
</head>
<style>
    .lists{
        width: 1200px;
        height: 100%;
    }
    .lists li{
        width: 500px;
        height: 50px;
        line-height: 50px;
        text-align: left;
        float: left;
    }
</style>
<body>
<div class="panel admin-panel">
   <ul class="lists">
       <li>服务器域名/IP：<?php echo $info['服务器域名/IP']; ?></li>
       <li>操作系统：<?php echo $info['操作系统']; ?></li>
       <li>运行环境：<?php echo $info['运行环境']; ?></li>
       <li>ThinkPHP版本：<?php echo $info['ThinkPHP版本']; ?></li>
       <li>上传附件限制：<?php echo $info['上传附件限制']; ?></li>
       <li>执行时间限制：<?php echo $info['执行时间限制']; ?></li>
       <li>服务器时间：<?php echo $info['服务器时间']; ?></li>
       <li>北京时间：<?php echo $info['北京时间']; ?></li>
       <li>剩余空间：<?php echo $info['剩余空间']; ?></li>
       <li>register_globals：<?php echo $info['register_globals']; ?></li>
       <li>magic_quotes_gpc：<?php echo $info['magic_quotes_gpc']; ?></li>
       <li>magic_quotes_runtime：<?php echo $info['magic_quotes_runtime']; ?></li>
   </ul>
</div>
</body>
<script>
    $(".file_upload").change(function(){
        var objUrl = getObjectURL(this.files[0]) ;
        // console.log("objUrl = "+objUrl);
        if (objUrl) {
            $(".img_upload").eq($(".file_upload").index(this)).attr("src", objUrl) ;
        }
    }) ;
    //建立一個可存取到該file的url
    function getObjectURL(file) {
        var url = null ; 
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }
</script>
</html>