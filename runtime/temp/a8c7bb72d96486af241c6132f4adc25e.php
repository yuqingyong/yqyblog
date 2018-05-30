<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"E:\phpStudy\WWW\yuqingyong\public/../application/home\view\Release\fabu.html";i:1516915804;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>发布需求 - 星辰网络博客</title>
	<meta name="keywords" content="星辰网络博客，星辰，星辰网络，星辰博客，博客，PHP，thinkphp">
    <meta name="description" content="一个个人的博客网站，有PHP，thinkphp，Linux等相关学习资料">
	<meta property="wb:webmaster" content="06779971d6009b5a">
	<link rel="alternate" type="application/rss+xml" title="星辰网络博客" href="/">
	<link href="http://www.thinkphp.cn/Public/favicon.ico" rel="shortcut icon">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/base.css" media="all">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/header.css" media="all">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/module.css" media="all">
	<script charset="utf-8" src="__PUBLIC__/admin/kindeditor/kindeditor.js"></script>
	<script charset="utf-8" src="__PUBLIC__/admin/kindeditor/lang/zh-CN.js"></script>
	</head>
	<style>
		#release{width: 100%;height: 25px;margin: 20px;}
		#release a{text-decoration: none;color: #0160A0;font-size: 28px;}
	</style>
	<script>
            KindEditor.ready(function(K) {
                    window.editor = K.create('#editor_id');
            });
            var options = {
                    cssPath : '/css/index.css',
                    filterMode : true
            };
            var editor = K.create('textarea[name="content"]', options);
            html = editor.html();
            // 同步数据后可以直接取得textarea的value
            editor.sync();
            html = document.getElementById('editor_id').value; // 原生API
            html = K('#editor_id').val(); // KindEditor Node API
            html = $('#editor_id').val(); // jQuery
            // 设置HTML内容
            editor.html('HTML内容');
            // 关闭过滤模式，保留所有标签
            // KindEditor.options.filterMode = false;

            // KindEditor.ready(function(K)) {
            //         K.create('#editor_id');
            // }
    </script>
	<body>
		<div id="release"><a href="<?php echo url('home/Release/index'); ?>">返回需求列表</a></div>
		<div class="main cf">
			<!-- 左边发布表单 -->
			<div class="wrapper">
				<form id="form" action="<?php echo url('home/Release/fabu'); ?>" method="post" class="think-form" enctype="multipart/form-data">
					<input type="hidden" name="__token__" value="<?php echo \think\Request::instance()->token(); ?>" />
					<!-- 头部用户信息 -->
					<div class="hd">
						<div class="avatar"><img src="__PUBLIC__/home/images/80_80.gif" alt=""></div>
						<div class="hd-info">
							<strong><?php echo \think\Session::get('users.username'); ?></strong>
							<span class="time"><?php echo date('Y-m-d',time()) ?></span>
						</div>
						<div class="hd-title">发布新需求</div>
					</div>
					<!-- /头部用户信息 -->

					<!-- 表单项 -->

					<table class="bd">
						<tbody>
							<tr>
								<th><i class="must">*</i>标题</th>
								<td class="cols-in"><input class="text" type="text" name="title" datatype="*1-50" nullmsg="标题不能为空" errormsg="长度太长"></td>
								<td><span class="Validform_checktip"></span></td>
							</tr>
							<tr>
								<th><i class="must">*</i>分类</th>
								<td>
									<select id="cate" name="type" datatype="*" nullmsg="分类不能为空">
										<option value="项目承接">项目承接</option>
										<option value="项目招募">项目招募</option>
										<option value="人才招聘">人才招聘</option>
									</select>
								</td>
								<td><span class="Validform_checktip"></span></td>
							</tr>
							<tr>
								<th><i class="must">*</i>内容</th>
								<td colspan="2">
									<div class="think-editor">
										<div class="enter">
											<textarea id="editor_id" name="content" style="height:300px;"></textarea>
										</div>
									</div>

								</td>
							</tr>
							<tr>
								<th>&nbsp;</th>
								<td colspan="2">
									<input class="submit" type="submit" value="提交">
									<span id="error_msg_show"></span>
								</td>
							</tr>
						</tbody>
					</table>

					<!-- /表单项 -->
				</form>
			</div>
			<!-- /左边发布表单 -->

			<!-- 边栏 -->
			<div class="sidebar">

				<div class="box key">
					<div class="hd" style="">发布需求</div>
					<ul class="bd">
						<li>请完整输入需求的标题和内容，并选择恰当的分类；</li>
						<li>根据自己的要求发布相应的内容，请勿发布恶意需求，谢谢！</li>
					</ul>
				</div>

				<!-- 快捷键小贴士 -->
				<div class="box key">
					<div class="hd">最新发布</div>
					<div class="bd">
						<div>
							<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
							<a href="<?php echo url('home/Release/rel_detail'); ?>"><?php echo substr($va['title'],0,16); ?><span>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $va['create_time']; ?></span></a>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					</div>
				</div>
				<!-- /快捷键小贴士 -->
			</div>
			<!-- /边栏 -->
		</div>
		<!-- 底部 -->
		<include file="Public/footer" />
		<!-- /底部 -->
	</body>

</html>