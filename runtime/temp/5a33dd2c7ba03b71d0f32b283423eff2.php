<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"E:\phpStudy\WWW\yuqingyong\public/../application/home\view\Chat\chat.html";i:1516915804;}*/ ?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>随心语录</title>
	<meta name="keywords" content="星辰网络博客，星辰，星辰网络，星辰博客，博客，PHP，thinkphp">
    <meta name="description" content="一个个人的博客网站，有PHP，thinkphp，Linux等相关学习资料">
	<meta http-equiv="Cache-Control" content="no-siteapp">
	<meta name="author" content="www.myweb.cn">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/bjy.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/index.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/bjy/animate.css">
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/nprogress.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/style.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/font-awesome.min.css">
    <link rel="apple-touch-icon-precomposed" href="__PUBLIC__/home/images/icon.png">
    <!-- <link rel="shortcut icon" href="images/favicon.ico"> -->
    <script src="__PUBLIC__/home/js/jquery-2.1.4.min.js"></script>
    <script src="__PUBLIC__/home/js/nprogress.js"></script>
    <script src="__PUBLIC__/home/js/layer.js"></script>
	</head>

<body class=" pace-done">
<header class="header">
  <nav class="navbar navbar-default" id="navbar">
    <div class="container">
      <div class="header-topbar hidden-xs link-border">
        <ul class="site-nav topmenu">
            <?php if(\think\Session::get('users.user_id') != ''): ?>
        		<li><a href="#}">欢迎您:<?php echo \think\Session::get('users.username'); ?></a></li>
        		<li><a href="<?php echo url('home/Index/login_out'); ?>">退出</a></li>
        	<?php else: ?>
          		<li><a href="<?php echo url('home/Index/login'); ?>">登录</a></li>
          	<?php endif; ?>
            <li><a href="<?php echo url('home/Index/register'); ?>" rel="nofollow" >注册</a></li>
            <li><a onclick="login()" title="第三方登录" >
                <i class="fa fa-rss">
                </i> 第三方登录
            </a></li>
        </ul>
                 勤记录 懂分享</div>
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar" aria-expanded="false"> <span class="sr-only"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <h1 class="logo hvr-bounce-in"><a href="<?php echo url('home/Index/index'); ?>" title="星辰网络博客"><img src="__PUBLIC__/home/images/logo.png" alt="星辰网络博客"></a></h1>
      </div>
      <div class="collapse navbar-collapse" id="header-navbar">
        <form class="navbar-form visible-xs" action="<?php echo url('home/Index/article_search'); ?>" method="get">
          <div class="input-group">
            <input type="text" name="word" class="form-control" placeholder="请输入关键字" maxlength="20" autocomplete="off">
            <span class="input-group-btn">
            <button class="btn btn-default btn-search" name="search" type="submit">搜索</button>
            </span> </div>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <li><a data-cont="星辰网络博客" title="星辰网络博客" href="<?php echo url('home/Index/index'); ?>">首页</a></li>
          <li><a data-cont="最新资讯" title="最新资讯" href="<?php echo url('home/Articles/news'); ?>">最新资讯</a></li>
          <li><a data-cont="IT技术笔记" title="IT技术笔记" href="<?php echo url('home/Articles/jishu'); ?>">IT技术笔记</a></li>
          <li><a data-cont="源码分享" title="404" href="<?php echo url('home/Articles/share'); ?>">源码分享</a></li>
          <li><a data-cont="随心笔记" title="随心笔记题" href="<?php echo url('home/Chat/chat'); ?>" >随心笔记</a></li>
          <li><a data-cont="需求发布" title="需求发布" href="<?php echo url('home/Release/index'); ?>" >需求发布</a></li>
          <li><a data-cont="心语心愿" title="心语心愿" href="<?php echo url('home/Chat/message'); ?>" >心语心愿</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
		<div id="b-content" class="container">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-8 b-chat">
					<div class="b-chat-left">
						<?php if(is_array($chat) || $chat instanceof \think\Collection || $chat instanceof \think\Paginator): $k = 0; $__LIST__ = $chat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($k % 2 );++$k;if($k%2 == 1): ?>
						<ul class="b-chat-one animated bounceInLeft">
							<li class="b-chat-title "><?php echo date('Y-m-d H:i:s',$va['create_time']); ?></li>
							<li class="b-chat-content"><?php echo $va['content']; ?></li>
							<div class="b-arrows-right1" style="top: 28.8px;">
								<div class="b-arrows-round"></div>
							</div>
							<div class="b-arrows-right2" style="top: 28.8px;"></div>
						</ul>
						<?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</div>
					<div class="b-chat-middle" style="height: 100%;"></div>
					<div class="b-chat-right">
						<?php if(is_array($chat) || $chat instanceof \think\Collection || $chat instanceof \think\Paginator): $k = 0; $__LIST__ = $chat;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($k % 2 );++$k;if($k%2 == 0): ?>
						<ul class="b-chat-one animated bounceInRight">
							<li class="b-chat-title "><?php echo date('Y-m-d H:i:s',$va['create_time']); ?></li>
							<li class="b-chat-content"><?php echo $va['content']; ?></li>
							<div class="b-arrows-right1" style="top: 45.6px;">
								<div class="b-arrows-round"></div>
							</div>
							<div class="b-arrows-right2" style="top: 45.6px;"></div>
						</ul>
						<?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</div>
				</div>
				<div id="b-public-right" class="col-lg-4 hidden-xs hidden-sm hidden-md">
					<div class="widget widget_sentence">
				        <h3>标签云</h3>
				        <div class="widget-sentence-content">
				            <ul class="plinks ptags">       
				            	<?php if(is_array($tags) || $tags instanceof \think\Collection || $tags instanceof \think\Paginator): $i = 0; $__LIST__ = $tags;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
				                <li><a href="<?php echo url('Home/Index/article_search',array('tid'=>$va['tid'])); ?>" title="<?php echo $va['tname']; ?>" draggable="false"><?php echo $va['tname']; ?></a></li>                
				                <?php endforeach; endif; else: echo "" ;endif; ?>
				            </ul>
				        </div>
				      </div>
					<div class="b-recommend">
						<h4 class="b-title">最热文章</h4>
						<p class="b-recommend-p">
							<?php if(is_array($hot_article) || $hot_article instanceof \think\Collection || $hot_article instanceof \think\Paginator): $i = 0; $__LIST__ = $hot_article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>  
							<a class="b-recommend-a" href="<?php echo url('Home/Articles/detail',array('aid'=>$va['aid'])); ?>" target="_blank"><span class="fa fa-th-list b-black"></span> <?php echo $va['title']; ?></a>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</p>
					</div>
					<div class="b-link">
						
					</div>
					<div class="b-search">
						<form class="form-inline" role="form" action="<?php echo url('home/Index/article_search'); ?>" method="get"> <input class="b-search-text" type="text" name="word"> <input class="b-search-submit" type="submit" value="全站搜索"></form>
					</div>
				</div>
			</div>
			<div class="row">
				<include file="Public/footer" />
			</div>
		</div>
		<script src="__PUBLIC__/home/bjy/hm.js"></script>
		<script src="__PUBLIC__/home/bjy/push.js"></script>
		<script src="__PUBLIC__/home/bjy/jquery-2.0.0.min.js"></script>
		<script>
			logoutUrl = "/Home/User/logout";
		</script>
		<script src="__PUBLIC__/home/bjy/bootstrap.min.js"></script>
		<script src="__PUBLIC__/home/bjy/pace.min.js"></script>
		<script src="__PUBLIC__/home/bjy/index.js"></script>
		<!-- 百度页面自动提交开始 -->
		<script>
			(function() {
				var bp = document.createElement('script');
				var curProtocol = window.location.protocol.split(':')[0];
				if(curProtocol === 'https') {
					bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
				} else {
					bp.src = 'http://push.zhanzhang.baidu.com/push.js';
				}
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(bp, s);
			})();
		</script>
		<!-- 百度页面自动提交结束 -->

		<!-- 百度统计开始 -->
		<script>
			var _hmt = _hmt || [];
			(function() {
				var hm = document.createElement("script");
				hm.src = "//hm.baidu.com/hm.js?c3338ec467285d953aba86d9bd01cd93";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hm, s);
			})();
		</script>
		<script>
			function login(){
				var txt = "<a href='http://www.baidu.com'><img src='/static/home/bjy/qq-login.png'></a>";
				layer.open({
				  type: 1,
				  skin: 'layui-layer-rim', //加上边框
				  area: ['420px', '240px'], //宽高
				  content: txt
				});
			}
		</script>
		<!-- 百度统计结束 -->
	</body>

</html>