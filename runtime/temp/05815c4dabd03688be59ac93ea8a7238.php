<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:85:"E:\phpStudy\WWW\yuqingyong\public/../application/home\view\Release\demand_detail.html";i:1516915804;s:60:"E:\phpStudy\WWW\yuqingyong\application\home\view\layout.html";i:1516915804;s:67:"E:\phpStudy\WWW\yuqingyong\application\home\view\public\header.html";i:1522915697;s:65:"E:\phpStudy\WWW\yuqingyong\application\home\view\public\rili.html";i:1516915804;s:67:"E:\phpStudy\WWW\yuqingyong\application\home\view\public\footer.html";i:1522915777;}*/ ?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>星辰网络博客</title>
    <meta name="keywords" content="星辰网络博客，星辰，星辰网络，星辰博客，博客，PHP，thinkphp">
    <meta name="description" content="一个个人的博客网站，有PHP，thinkphp，Linux等相关学习资料">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/nprogress.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/style.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/prettyprint.css">
    <link rel="stylesheet" type="text/css" href="__PUBLIC__/home/css/font-awesome.min.css">
    <link rel="apple-touch-icon-precomposed" href="/tpl/Home/Public/images/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <script src="__PUBLIC__/home/js/jquery-2.1.4.min.js"></script>
    <script src="__PUBLIC__/home/js/nprogress.js"></script>
  	<script src="__PUBLIC__/home/js/layer.js"></script>
  	<link rel="stylesheet" type="text/css" href="__PUBLIC__/home/rili/rilistyle.css">
	<script type="text/javascript" src="__PUBLIC__/home/rili/main.js"></script>
    <!--<script src="/tpl/Home/Public/js/jquery.lazyload.min.js"></script>-->
    <!--[if gte IE 9]>
      <script src="js/jquery-1.11.1.min.js" type="text/javascript"></script>
      <script src="js/html5shiv.min.js" type="text/javascript"></script>
      <script src="js/respond.min.js" type="text/javascript"></script>
      <script src="js/selectivizr-min.js" type="text/javascript"></script>
    <![endif]-->
    <!--[if lt IE 9]>
      <script>window.location.href='upgrade-browser.html';</script>
    <![endif]-->
</head>
<body>
<header class="header">
  <nav class="navbar navbar-default" id="navbar">
    <div class="container">
      <div class="header-topbar hidden-xs link-border">
        <ul class="site-nav topmenu">
          <?php if(\think\Session::get('users') != ''): ?>
            <li><a href="#}">欢迎您:<?php echo \think\Session::get('users.username'); ?></a></li>
            <li><a href="<?php echo url('home/Index/log_out'); ?>">退出</a></li>
          <?php else: ?>
              <li><a href="<?php echo url('home/Index/login'); ?>">登录</a></li>
          <?php endif; ?>
            <li><a href="<?php echo url('home/Index/register'); ?>" rel="nofollow" >注册</a></li>
            <li><a  onclick="login()" title="第三方登录" >
                <i class="fa fa-rss">
                </i> 第三方登录
            </a></li>
            <!--<li><a href="<?php echo url('home/Index/upload'); ?>">文件下载</a></li>-->
        </ul> 勤记录 懂分享</div>
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar" aria-expanded="false"> <span class="sr-only"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <h1 class="logo hvr-bounce-in"><a href="<?php echo url('home/Index/index'); ?>" title="星辰网络博客"><img src="__PUBLIC__/home/images/logo.png" alt="星辰网络博客"></a></h1>
      </div>
      <div class="collapse navbar-collapse" id="header-navbar">
        <form class="navbar-form visible-xs" action="<?php echo url('home/Index/article_search'); ?>" method="post">
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
<script>
  function login(){
    var txt = "<a href='#'><img src='/static/home/bjy/qq-login.png'></a>";
    layer.open({
      type: 1,
      skin: 'layui-layer-rim', //加上边框
      area: ['420px', '240px'], //宽高
      content: txt
    });
  }
</script>
 <section class="container">
	<div class="content-wrap">
		<div class="content">
			<div style="text-align: right;"><a href="#">私信他（她）</a></div>
			<header class="article-header">
				<h1 class="article-title"><a  title="<?php echo $art_detail['title']; ?>" ><?php echo $art_detail['title']; ?></a></h1>
				<div class="article-meta"> <span class="item article-meta-time">
		          <time class="time" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="发表时间：<?php echo $art_detail['create_time']; ?>"><i class="glyphicon glyphicon-time"></i> <?php echo $art_detail['create_time']; ?></time>
		          </span> <span class="item article-meta-source" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="来源：<?php echo $art_detail['type']; ?>"><i class="glyphicon glyphicon-globe"></i> <?php echo $art_detail['type']; ?></span> <span class="item article-meta-category" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="MZ-NetBlog主题"><i class="glyphicon glyphicon-list"></i>
                  需求详情</a></span> <span class="item article-meta-views" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="浏览量：<?php echo $art_detail['see_num']; ?>"><i class="glyphicon glyphicon-eye-open"></i> <?php echo $art_detail['see_num']; ?></span> <span class="item article-meta-comment" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="评论量"><i class="glyphicon glyphicon-comment"></i> <?php echo $art_detail['comment_num']; ?></span> </div>
			</header>
			<article class="article-content">
				<?php echo $art_detail['content']; ?>
			</article>
		</div>
	</div>
	<aside class="sidebar">
		<div class="fixed">
			<div class="widget widget-tabs">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#notice" aria-controls="notice" role="tab" data-toggle="tab" draggable="false">统计信息</a>
					</li>
					<li role="presentation">
						<a href="#contact" aria-controls="contact" role="tab" data-toggle="tab" draggable="false">联系站长</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane contact active" id="notice">
						<h2>日志总数:<?php echo $web['all_article_num']; ?>篇
              </h2>
						<h2>网站运行:
              <span id="sitetime"><?php echo $web['web_day']; ?>天 </span></h2>
					</div>
					<div role="tabpanel" class="tab-pane contact" id="contact">
						<h2>QQ:
                  <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=1425219094&amp;site=qq&amp;menu=yes" target="_blank" rel="nofollow" data-toggle="tooltip" data-placement="bottom" title=""  data-original-title="QQ:1425219094">1425219094</a>
              </h2>
              <h2>Email:
              <a href="mailto:1425219094@qq.com" target="_blank" data-toggle="tooltip" rel="nofollow" data-placement="bottom" title=""  data-original-title="Email:1425219094@qq.com">1425219094@qq.com</a></h2>
					</div>
				</div>
			</div>
			<div class="widget widget_search">
				<form class="navbar-form" action="<?php echo url('home/Index/article_search'); ?>" method="post">
					<div class="input-group">
						<input type="text" name="word" class="form-control" size="35" placeholder="请输入关键字" maxlength="15" autocomplete="off">
						<span class="input-group-btn">
			            <button class="btn btn-default btn-search" name="search" type="submit">搜索</button>
			            </span> 
			        </div>
				</form>
			</div>
		</div>
		<div class="widget widget_hot">
			<h3>最热文章</h3>
			<ul>
				<?php if(is_array($hot_article) || $hot_article instanceof \think\Collection || $hot_article instanceof \think\Paginator): $i = 0; $__LIST__ = $hot_article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>  
                <li><a title="<?php echo $va['title']; ?>" href="<?php echo url('home/Articles/detail',array('aid'=>$va['aid'])); ?>" ><span class="thumbnail">
                    <img class="thumb" data-original="<?php echo $va['path']; ?>" src="<?php echo $va['path']; ?>" alt="<?php echo $va['title']; ?>"  style="display: block;">
                </span><span class="text"><?php echo $va['title']; ?></span><span class="muted"><i class="glyphicon glyphicon-time"></i>
                    <?php echo $va['create_time']; ?>
                </span><span class="muted"><i class="glyphicon glyphicon-eye-open"></i><?php echo $va['click']; ?></span></a></li>
            	<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div class="widget widget_sentence" style="width:360px;height:100%;">    
<div id="content">
<!-- 显示日期详情 -->
<div id="detail">
   <div id="date_content"></div>

   <div id="animal_year"></div>
    <!-- 显示时钟 -->
    <p id="clock"></p>
</div>
<form name="calender_content" style="margin-top: -15px;">
              <table class="week">
                <tbody>
                   <tr>
                      <td class="ch">
                              <!-- 日历头部 -->
                             <table>
                                 <tbody>
                                       <tr class="day">
                                              <td>日</td>
                                              <td>一</td>
                                              <td>二</td>
                                              <td>三</td>
                                              <td>四</td>
                                              <td>五</td>
                                              <td>六</td>
                                          </tr>
                                     </tbody>
                              </table>
                              </tr>
                              </td>
                                <!-- 头部END -->
                             
             <!-- js动态的向表格写入日期 -->
                        <script>
                        var Num; //Num计算出日期位置
                        for(i=0;i<6;i++) {

                                document.write('<table id="cal-content"><tr>');
                               
                         for(j=0;j<7;j++) {
                            Num = i*7+j;
                            document.write('<td id="SD' + Num +'" onclick="addDay(' + Num +')" ');
                    //周六 周日 假期样式设定
                          if(j == 0|| j == 6)
                          {
                                document.write(' class="aorange"');
                           }else{
                                document.write(' class="one"');
                          }
                                document.write('title=""> </td>')
                         }

                            document.write('</tr></table></td></tr><tr><td><table style="width:399;"><tr style="text-align:center"> ');
                       //农历
                       for(j=0;j<7;j++) {
                          Num = i*7+j;
                          document.write('<td id="LD' + Num +'" onclick="addDay(' + Num +')" class="bs" title=""> </td>')

                       }
                          document.write('</tr></table></td></tr>');
                       
                     }
                     </script>  
         <!-- 生成日期 END    -->
                    </tr>
                 </table>
               </td>
            </tr>   
          </tbody>
        </table>
         <table>
           <tbody>
            <tr>
              <td class="sm">
                <table class="table_head">
                  <tbody>
                  <tr>
                    <td> 
                    <!-- 选择年份菜单 -->
                      <div class="year_select">
                          <span onClick="BtN('year_d')"><img src="__PUBLIC__/home/rili/left.png"></span>
                            <select onChange="chaCld()" name="SY">
                              <script>
                                 for(i=1900;i<2050;i++) 
                                document.write('<option>'+i+"年")
                              </script>
                            </select> 
                          <span onClick="BtN('year_a')"><img src="__PUBLIC__/home/rili/right.png"></span> 
                      </div>
                      <!-- 回到当天菜单 -->
                      <div  class="home_select">
                           <span onClick="BtN('')"><img src="__PUBLIC__/home/rili/2.png" style="width:26px;height:26px"></span>
                      </div>

                      <!-- 选择月份菜单 -->
                          <div style="display:inline-block;">
                              <span onClick="BtN('month_d')"><img src="__PUBLIC__/home/rili/left.png"></span>
                               <select onChange="chaCld()" name="SM">
                                <script>
                                for(i=1;i<13;i++) document.write('<option>'+i+"月")
                                </script>
                                </select> 
                              <span onClick="BtN('month_a')"><img src="__PUBLIC__/home/rili/right.png"></span>
                          </div>
                       </td>
                   </tr>
                 </tbody>
                </table>
              </td>
            </tr> 
         </tbody>
       </table>
  </form>
</div> 
</div>
<!--日历插件结束-->
<script>
window.onload=function(){
     initial();
}
</script>
	    <div class="widget widget_sentence">    
	        <a href="__PUBLIC__/home/images/ad2.jpg" target="_blank" rel="nofollow" title="广告位二" >
	        <img style="width: 100%" src="__PUBLIC__/home/images/ad2.jpg" alt="广告位二" ></a>    
	    </div>
	</aside>
</section>
</body>

</html>
<footer class="footer">
  <div class="container">
    <p>本站[<a href="http://www.muzhuangnet.com/" >星辰网络博客</a>]的部分内容来源于网络，若侵犯到您的利益，请联系站长删除！谢谢！Powered By [<a href="/" target="_blank" rel="nofollow" >DTcms</a>] Version 4.0 &nbsp;<a href="/" target="_blank" rel="nofollow" >赣ICP备17010344</a> &nbsp; <a href="/" target="_blank" class="sitemap" >网站地图</a></p>
  </div>
  <div id="gotop"><a class="gotop"></a></div>
</footer>
 <script src="__PUBLIC__/home/js/bootstrap.min.js"></script>
<script src="__PUBLIC__/home/js/jquery.ias.js"></script>
<script src="__PUBLIC__/home/js/prettyprint.js"></script>
<!-- <script src="__PUBLIC__/home/js/scripts.js"></script> -->