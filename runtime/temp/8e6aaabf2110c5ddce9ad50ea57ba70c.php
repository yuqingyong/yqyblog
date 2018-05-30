<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"E:\phpStudy\WWW\yuqingyong\public/../application/home\view\Chat\message.html";i:1516915804;}*/ ?>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="__PUBLIC__/home/liuyan/base.css" type="text/css"/>
        <link rel="stylesheet" href="__PUBLIC__/home/liuyan/tan.css" type="text/css"/>
        
        <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
        <script src="__PUBLIC__/home/liuyan/main.js"></script>
        <script src="__PUBLIC__/home/liuyan/index.js" type="text/javascript"></script>
        <title>心语墙</title>
    </head>
    <body>
        <div class="main">
            <div class="main_nav">
            	<ul>
            		<li><a class="cd-signin" href="#0">写一句话</a></li>
					<li><a  href="/">返回首页</a></li>
            	</ul>
            </div>
            <?php if(is_array($message) || $message instanceof \think\Collection || $message instanceof \think\Paginator): $k = 0; $__LIST__ = $message;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($k % 2 );++$k;if($k % 2 == 0): ?>
            <div name="note" class="note">
                <div class="nhead" style="background-image: url(__PUBLIC__/home/liuyan/a5_1.gif);">
                    <?php echo date('Y-m-d',$va['create_time']); ?>
                </div>
                <div class="nbody" style="background-image: url(__PUBLIC__/home/liuyan/a5_2.gif);">
                     <?php echo $va['content']; ?>
                </div>
                <div class="nfoot" style="background-image: url(__PUBLIC__/home/liuyan/a5_3.gif);">
                    <div class="moodpic">
                        <img src="__PUBLIC__/home/liuyan/17.gif"/>
                    </div>
                    <div class="username">
                        <?php echo $va['nickname']; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div name="note" class="note">
                <div class="nhead" style="background-image: url(__PUBLIC__/home/liuyan/a1_1.gif);">
                    <?php echo date('Y-m-d',$va['create_time']); ?>
                </div>
                <div class="nbody" style="background-image: url(__PUBLIC__/home/liuyan/a1_2.gif);">
                     <?php echo $va['content']; ?>
                </div>
                <div class="nfoot" style="background-image: url(__PUBLIC__/home/liuyan/a1_3.gif);">
                    <div class="moodpic">
                        <img src="__PUBLIC__/home/liuyan/3.gif"/>
                    </div>
                    <div class="username">
                        <?php echo $va['nickname']; ?>
                    </div>
                </div>
            </div>
            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>

<!--弹出表单开始-->
<div class="cd-user-modal" style="z-index:99999"> 
<div class="cd-user-modal-container">
	<ul class="cd-switcher" style="padding:0">
		<li><a href="#0">写下心中想说的话</a></li>
	</ul>

	<div id="cd-login"> <!-- 登录表单 -->
		<form class="cd-form" action="<?php echo url('home/Chat/message'); ?>" method="post">
			<p class="fieldset">
				<textarea type="text" maxlength="100"  name="content" style="width:530px;height:100px;" placeholder="请输入你想说的话（最多100个字符）"/></textarea>
			</p>

			<p class="fieldset">
				<input class="full-width has-padding has-border" id="signin-password" name="nickname" type="text" maxlength="5" placeholder="留名(默认为：游客)，最多5个字符">
			</p>

			<p class="fieldset">
				<input class="full-width2" type="submit" value="提 交">
			</p>
		</form>
	</div>
	<a href="#0" class="cd-close-form">关闭</a>
</div>
</div>
<!--弹出表单结束-->
</body>
</html>