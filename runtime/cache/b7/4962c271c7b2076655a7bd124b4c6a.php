<?php
//000000086400a:10:{i:0;a:15:{s:3:"aid";i:170;s:5:"title";s:42:"PHP微信商户企业向用户零钱付款";s:11:"create_time";i:1530006039;s:3:"cid";i:2;s:7:"content";s:12867:"<p>
	这一个月一直忙着做公司的一个网页游戏项目；
</p>
<p>
	其中遇到个需求，就是需要在后台添加一个一键提现的功能；
</p>
<p>
	需要的是使用微信商户对接企业向用户零钱发起付款；
</p>
<p>
	首先我们需要去申请一个微信商户号，并设置好相关的参数：
</p>
<p>
	官方的开发文档：<a href="https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2" target="_blank">https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_2</a> 
</p>
<p>
	<span>直接贴代码了：</span> 
</p>
<p>
	<span>首先是前端部分：</span> 
</p>
<p>
	<span> </span> 
</p>
<pre class="prettyprint lang-js">function recharge(){
    obj = document.getElementsByName("amount_id");
    check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    //获取单选框的值
    var status = $('input[name="status_type"]:checked').val();

    if(check_val.length == 0) return layer.msg('您还未选择处理的数据');

    //判断处理状态提示
    if(status == 0){
    	var type = "返还";
    }else if(status == 2){
    	var type = "提现";
    }else if (status == 3) {
    	var type = "冻结";
    }else{
    	return layer.msg('请选择处理方式');
    }

    layer.confirm('确认要一键处理'+type+'吗？',function(index){
    	var loading = layer.load(2, {
		  shade: [0.5,'#fff'] //0.1透明度的白色背景
		});

    	$.post('/admin/Users/amount_deal',{'amount_id':JSON.stringify(check_val),'status':status},function(res){
			var _json = JSON.parse(res);
			if(_json.status == 1){
				layer.msg(_json.msg);
				setTimeout(reload_page, 3000);
			}else{
				layer.msg(_json.msg);
			}
			layer.close(loading);
		})
	});
}</pre>
通过ajax请求后台接口，携带订单ID参数，提现状态
<p>
	<br />
</p>
<p>
	<span>后台处理部分：</span> 
</p>
<p>
	<span> </span> 
</p>
<pre class="prettyprint lang-php">//提现申请根据提交状态处理  0：返还 2：提现 3：冻结
    public function amount_deal(Request $request)
    {
        $amount_ids = json_decode($this-&gt;request-&gt;post('amount_id'),true);
        $status = $this-&gt;request-&gt;post('status');
  
        if($status == null) return json_encode(['status'=&gt;0,'msg'=&gt;'请选择处理方式']);

        if(empty($amount_ids)) return json_encode(['status'=&gt;0,'msg'=&gt;'提交的处理数据为空']);

        # 提现批量处理
        if($status == 0){
            # 返还处理
            foreach ($amount_ids as $k =&gt; $v) {
                $amount = Db::name('amount')-&gt;where('id',$v)-&gt;field('status,uid,amount_cash')-&gt;find();
                if($amount['status'] != 2 &amp;&amp; $amount['status'] != 0){
                    # 返还操作
                    Db::name('users')-&gt;where('uid',$amount['uid'])-&gt;setInc('bonus',$amount['amount_cash']);
                    # 修改提现申请状态
                    Db::name('amount')-&gt;where('id',$v)-&gt;update(['status'=&gt;$status]);
                }
            }
            return json_encode(['status'=&gt;1,'msg'=&gt;'返还操作成功']);
        }elseif ($status == 3) {
            # 冻结处理
            foreach ($amount_ids as $k =&gt; $v) {
                $amount = Db::name('amount')-&gt;where('id',$v)-&gt;field('status')-&gt;find();
                if($amount['status'] != 2 &amp;&amp; $amount['status'] != 0){
                    # 修改提现申请状态为3冻结
                    Db::name('amount')-&gt;where('id',$v)-&gt;update(['status'=&gt;$status]);
                }
            }
            return json_encode(['status'=&gt;1,'msg'=&gt;'冻结操作成功']);
        }elseif ($status == 2) {
            sleep(2);
            # 在这里就开始判断提交的申请用户是否都包含有openid，否则直接返回fasle
            foreach ($amount_ids as $k =&gt; $v) {
                $amount_list = Db::name('amount')-&gt;where('id',$v)-&gt;field('status,uid,type')-&gt;find();//判断用户是否存在openid
                $user = Db::name('users')-&gt;where('uid',$amount_list['uid'])-&gt;field('openid,username')-&gt;find();
                //判断提现类型是否为微信
                if($amount_list['type'] != 1){
                    return json_encode(['status'=&gt;0,'msg'=&gt;'用户名为【'.$user['username'].'】的申请非微信提现类型,请重新选择！']);die;
                }

                if(empty($user['openid'])){
                    return json_encode(['status'=&gt;0,'msg'=&gt;'用户名为【'.$user['username'].'】的申请未包含openid,请重新选择！']);die;
                }
            }

            # 提现处理
            require_once EXTEND_PATH.'wxpay'.DS.'Recharge.php';
            $recharge = new \Recharge();
            # 根据提交的订单ID批量处理
            foreach ($amount_ids as $k =&gt; $v) {
                $amount = Db::name('amount')-&gt;where('id',$v)-&gt;field('status,order_number,amount_cash,uid')-&gt;find();
                if($amount['status'] != 2 &amp;&amp; $amount['status'] != 0){
                    # 查询用户信息
                    $user = Db::name('users')-&gt;where('uid',$amount['uid'])-&gt;field('openid')-&gt;find();
                    //参数数组
                    $data = [
                        'openid' =&gt; $user['openid'],
                        'price'  =&gt; $amount['amount_cash']*100,
                        'order_number' =&gt; $amount['order_number'],
                    ];
                    $res = $recharge-&gt;comPay($data);
                    if($res['result_code'] &amp;&amp; $res['result_code'] === 'SUCCESS'){
                        //将该条申请数据改为已提现
                        Db::name('amount')-&gt;where('id',$v)-&gt;update(['status'=&gt;$status]);
                    }else{
                        return json_encode(['status'=&gt;0,'msg'=&gt;$res['err_code_des']]);
                    }
                }
            }
            return json_encode(['status'=&gt;1,'msg'=&gt;'提现处理成功']);
        }else{
            return json_encode(['status'=&gt;0,'msg'=&gt;'参数错误']);
        }
    }</pre>
主要是看状态为2的部分，因为我做的是一键处理订单，状态有几个，所以分类型处理，主要还是提现部分
<p>
	<br />
</p>
<p>
	<span>因为是批量处理，所以使用foreach循环逐个处理，一旦发现有不符合条件的就返回错误信息；</span> 
</p>
<p>
	<span>不过我在发送提现的请求给微信之前，就已经做了是否存在openid和是否为微信提现的判断，以保证提现不出先openid不存在或者用户不存在的问题；</span> 
</p>
<p>
	<span>接下来是提现类的处理：</span>
</p>
<p>
	<span> </span>
</p>
<pre class="prettyprint lang-php">&lt;?php
/**
*企业向用户零钱提现类
*/
include 'RegBase.php';
class Recharge extends RegBase
{
	private $params;
    //微信付款接口地址
    const PAYURL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    //发起提现
    public function comPay($data){
        //构建原始数据
        $this-&gt;params = [
            'mch_appid'         =&gt; self::APPID,//APPid,
            'mchid'             =&gt; self::MCHID,//商户号,
            'nonce_str'         =&gt; md5(time()), //随机字符串
            'partner_trade_no'  =&gt; $data['order_number'], //商户订单号
            'openid'            =&gt; $data['openid'], //用户openid
            'check_name'        =&gt; 'NO_CHECK',//校验用户姓名选项 NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
            //'re_user_name'    =&gt; '',//收款用户姓名  如果check_name设置为FORCE_CHECK，则必填用户真实姓名
            'amount'            =&gt; $data['price'],//金额 单位分
            'desc'              =&gt; '测试付款',//付款描述
            'spbill_create_ip'  =&gt; $_SERVER['SERVER_ADDR'],//调用接口机器的ip地址
        ];
        //将数据发送到接口地址
        return $this-&gt;send(self::PAYURL);
    }
    //签名
    public function sign(){
        return $this-&gt;setSign($this-&gt;params);
    }
    //发送请求
    public function send($url){
        $res = $this-&gt;sign();
        $xml = $this-&gt;ArrToXml($res);
       $returnData = $this-&gt;postData($url, $xml);
       return $this-&gt;XmlToArr($returnData);
    }
}</pre>
通过传递一个参数数组进行发起提现，其中包含一下RegBase中的签名函数和加密函数，以及XML和数组互转函数
<p>
	<br />
</p>
<p>
	<span>
<pre class="prettyprint lang-php">&lt;?php
class  RegBase
{
    const KEY = '*****************************'; //请修改为自己的
    const MCHID = '*********'; //请修改为自己的
    const RPURL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    const APPID = '************';//请修改为自己的
    const CODEURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const OPENIDURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    const SECRET = '**********************';//请修改为自己的
    //获取用户openid 为避免重复请求接口获取后应做存储
   
	/**  
	* 获取签名 
	* @param array $arr
	* @return string
	*/  
    public function getSign($arr){
        //去除空值
        $arr = array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成url格式的字符串
       $str = $this-&gt;arrToUrl($arr) . '&amp;key=' . self::KEY;
       return strtoupper(md5($str));
    }
    /**  
	* 获取带签名的数组 
	* @param array $arr
	* @return array
	*/  
    public function setSign($arr){
        $arr['sign'] = $this-&gt;getSign($arr);;
        return $arr;
    }
	/**  
	* 数组转URL格式的字符串
	* @param array $arr
	* @return string
	*/
    public function arrToUrl($arr){
        return urldecode(http_build_query($arr));
    }
    
    //数组转xml
    function ArrToXml($arr)
    {
            if(!is_array($arr) || count($arr) == 0) return '';

            $xml = "&lt;xml&gt;";
            foreach ($arr as $key=&gt;$val)
            {
                    if (is_numeric($val)){
                            $xml.="&lt;".$key."&gt;".$val."&lt;/".$key."&gt;";
                    }else{
                            $xml.="&lt;".$key."&gt;&lt;![CDATA[".$val."]]&gt;&lt;/".$key."&gt;";
                    }
            }
            $xml.="&lt;/xml&gt;";
            return $xml; 
    }
	
    //Xml转数组
    function XmlToArr($xml)
    {	
            if($xml == '') return '';
            libxml_disable_entity_loader(true);
            $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
            return $arr;
    }

    function postData($url,$postfields){
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $postfields;
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        //以下是证书相关代码
        $params[CURLOPT_SSLCERTTYPE] = 'PEM';
        $params[CURLOPT_SSLCERT] = '../extend/wxpay/cert/apiclient_cert.pem';
        $params[CURLOPT_SSLKEYTYPE] = 'PEM';
        $params[CURLOPT_SSLKEY] = '../extend/wxpay/cert/apiclient_key.pem';

        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
}</pre>
需要注意的一点是，我这个项目是在TP5的框架下开发的，所以在写CA证书的路径的时候需要填写</span>
</p>
<p>
	<span>../extend/wxpay/cert/apiclient_cert.pem<br />
因为TP5默认指向的是public，如果使用 ./cert/<span>apiclient_cert.pem是无法读取的，就会返回CA证书错误的提示；</span></span>
</p>
<p>
	<span>另外附上一个文件存放目录的图片</span>
</p>
<p>
	<span><img src="/static/admin/kindeditor/attached/image/20180626/20180626174028_27268.png" alt="" /></span>
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:18:"微信商户提现";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:91;s:5:"click";i:17;s:11:"comment_num";i:0;s:11:"description";s:42:"PHP微信商户企业向用户零钱付款";s:5:"ap_id";i:74;s:4:"path";s:20:"/upload/170thumb.png";}i:1;a:15:{s:3:"aid";i:169;s:5:"title";s:14:"JS学习笔记";s:11:"create_time";i:1529137463;s:3:"cid";i:7;s:7:"content";s:19147:"# javascript笔记<br />
<br />
---<br />
###一.数据类型和变量<br />
1. javascript不区分整数和浮点数，统一用Number表示 NaN表示Not a Number，无法计算结果时用<br />
2. Infinity表示无限大，当数值超过了Javascript的Number所能表示的最大值时，就表示为Infinity<br />
3.&nbsp; NaN === NaN; // false，唯一能判断NaN的方法是通过isNaN()函数<br />
4.&nbsp; 1 / 3 === (1 - 2 / 3); // false浮点数在运算过程中会产生误差，因为计算机无法精确表示无限循环小数。要比较两个浮点数是否相等，只能计算它们之差的绝对值，看是否小于某个阈值Math.abs(1 / 3 - (1 - 2 / 3)) &lt; 0.0000001; // true<br />
<br />
###二.字符串和数组，对象<br />
1. 如果字符串内部既包含'又包含"怎么办？可以用转义字符\来标识，比如：'I\'m \"OK\"!';<br />
2. indeOf() 搜索一个指定的元素的位置<br />
3. slice() 截取数组的部分元素，然后返回一个新的数组<br />
4. push和pop 向数组的末尾添加（push）或者删除（pop）一个元素，返回数组的新长度<br />
5. unshift和shift 如果要往Array的头部添加若干元素，使用unshift()方法，shift()方法则把Array的第一个元素删掉<br />
6. sort()可以对当前Array进行排序，它会直接修改当前Array的元素位置，直接调用时，按照默认顺序排序<br />
7. reverse()把整个Array的元素给掉个个，也就是反转<br />
8. splice()方法是修改Array的“万能方法”，它可以从指定的索引开始删除若干元素，然后再从该位置添加若干元素<br />
9. concat()方法把当前的Array和另一个Array连接起来，并返回一个新的Array<br />
10. join()方法是一个非常实用的方法，它把当前Array的每个元素都用指定的字符串连接起来，然后返回连接后的字符串<br />
```js<br />
var xiaoming = {<br />
&nbsp; &nbsp; name: '小明'<br />
};<br />
xiaoming.age; // undefined<br />
xiaoming.age = 18; // 新增一个age属性<br />
xiaoming.age; // 18<br />
delete xiaoming.age; // 删除age属性<br />
xiaoming.age; // undefined<br />
delete xiaoming['name']; // 删除name属性<br />
xiaoming.name; // undefined<br />
delete xiaoming.school; // 删除一个不存在的school属性也不会报错<br />
//如果我们要检测xiaoming是否拥有某一属性，可以用in操作符<br />
'name' in xiaoming; // true<br />
'grade' in xiaoming; // false<br />
//不过要小心，如果in判断一个属性存在，这个属性不一定是xiaoming的，它可能是xiaoming继承得到的<br />
'toString' in xiaoming; // true<br />
//因为toString定义在object对象中，而所有对象最终都会在原型链上指向object，所以xiaoming也拥有toString属性。<br />
<br />
//要判断一个属性是否是xiaoming自身拥有的，而不是继承得到的，可以用hasOwnProperty()方法<br />
var xiaoming = {<br />
&nbsp; &nbsp; name: '小明'<br />
};<br />
xiaoming.hasOwnProperty('name'); // true<br />
xiaoming.hasOwnProperty('toString'); // false<br />
```<br />
- 注意：JavaScript把null、undefined、0、NaN和空字符串''视为false，其他值一概视为true<br />
<br />
###三.Map和Set,iterable<br />
```js<br />
###Map是一组键值对的结构，具有极快的查找速度。###<br />
var m = new Map([['Michael', 95], ['Bob', 75], ['Tracy', 85]]);<br />
m.get('Michael'); // 95<br />
//初始化Map需要一个二维数组，或者直接初始化一个空Map。Map具有以下方法<br />
var m = new Map(); // 空Map<br />
m.set('Adam', 67); // 添加新的key-value<br />
m.set('Bob', 59);<br />
m.has('Adam'); // 是否存在key 'Adam': true<br />
m.get('Adam'); // 67<br />
m.delete('Adam'); // 删除key 'Adam'<br />
m.get('Adam'); // undefined<br />
<br />
###Set###<br />
//Set和Map类似，也是一组key的集合，但不存储value。由于key不能重复，所以，在Set中，没有重复的key。<br />
//要创建一个Set，需要提供一个Array作为输入，或者直接创建一个空Set<br />
var s1 = new Set(); // 空Set<br />
var s2 = new Set([1, 2, 3]); // 含1, 2, 3<br />
//重复元素在Set中自动被过滤<br />
var s = new Set([1, 2, 3, 3, '3']);<br />
s; // Set {1, 2, 3, "3"}<br />
//通过add(key)方法可以添加元素到Set中，可以重复添加，但不会有效果<br />
s.add(4);<br />
s; // Set {1, 2, 3, 4}<br />
//通过delete(key)方法可以删除元素<br />
var s = new Set([1, 2, 3]);<br />
s; // Set {1, 2, 3}<br />
s.delete(3);<br />
s; // Set {1, 2}<br />
<br />
###iterable###<br />
//用for ... of循环遍历集合，用法如下<br />
var a = ['A', 'B', 'C'];<br />
var s = new Set(['A', 'B', 'C']);<br />
var m = new Map([[1, 'x'], [2, 'y'], [3, 'z']]);<br />
for (var x of a) { // 遍历Array<br />
&nbsp; &nbsp; console.log(x);<br />
}<br />
for (var x of s) { // 遍历Set<br />
&nbsp; &nbsp; console.log(x);<br />
}<br />
for (var x of m) { // 遍历Map<br />
&nbsp; &nbsp; console.log(x[0] + '=' + x[1]);<br />
}<br />
<br />
//使用iterable内置的forEach方法，它接收一个函数，每次迭代就自动回调该函数。以Array为例<br />
'use strict';<br />
var a = ['A', 'B', 'C'];<br />
a.forEach(function (element, index, array) {<br />
&nbsp; &nbsp; // element: 指向当前元素的值<br />
&nbsp; &nbsp; // index: 指向当前索引<br />
&nbsp; &nbsp; // array: 指向Array对象本身<br />
&nbsp; &nbsp; console.log(element + ', index = ' + index);<br />
});<br />
<br />
//打印结果<br />
A, index = 0<br />
B, index = 1<br />
C, index = 2<br />
<br />
//Map的回调函数参数依次为value、key和map本身<br />
var m = new Map([[1, 'x'], [2, 'y'], [3, 'z']]);<br />
m.forEach(function (value, key, map) {<br />
&nbsp; &nbsp; console.log(value);<br />
});<br />
```<br />
- 免费关键字：利用arguments，你可以获得调用者传入的所有参数。也就是说，即使函数不定义任何参数，还是可以拿到参数的值<br />
- 实际上arguments最常用于判断传入参数的个数。你可能会看到这样的写法<br />
```js<br />
// foo(a[, b], c)<br />
// 接收2~3个参数，b是可选参数，如果只传2个参数，b默认为null：<br />
function foo(a, b, c) {<br />
&nbsp; &nbsp; if (arguments.length === 2) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; // 实际拿到的参数是a和b，c为undefined<br />
&nbsp; &nbsp; &nbsp; &nbsp; c = b; // 把b赋给c<br />
&nbsp; &nbsp; &nbsp; &nbsp; b = null; // b变为默认值<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; // ...<br />
}<br />
```<br />
###高阶函数<br />
<br />
- map<br />
```js<br />
function pow(x) {<br />
&nbsp; &nbsp; return x * x;<br />
}<br />
//在map方法中传入参数，即函数本身，函数会对数组中的每个元素做处理<br />
var arr = [1, 2, 3, 4, 5, 6, 7, 8, 9];<br />
var results = arr.map(pow); // [1, 4, 9, 16, 25, 36, 49, 64, 81]<br />
```<br />
- reduce<br />
```js<br />
//比方说对一个Array求和，就可以用reduce实现<br />
var arr = [1, 3, 5, 7, 9];<br />
arr.reduce(function (x, y) {<br />
&nbsp; &nbsp; return x + y;<br />
})<br />
```<br />
- filter<br />
```js<br />
//filter也是一个常用的操作，它用于把Array的某些元素过滤掉，然后返回剩下的元素,根据返回值是true还是false决定保留还是丢弃该元素<br />
var arr = [1, 2, 4, 5, 6, 9, 10, 15];<br />
var r = arr.filter(function (x) {<br />
&nbsp; &nbsp; return x % 2 !== 0;<br />
});<br />
<br />
//回调函数<br />
//filter()接收的回调函数，其实可以有多个参数。通常我们仅使用第一个参数，表示Array的某个元素。回调函数还可以接收另外两个参数，表示元素的位置和数组本身：<br />
<br />
var arr = ['A', 'B', 'C'];<br />
var r = arr.filter(function (element, index, self) {<br />
&nbsp; &nbsp; console.log(element); // 依次打印'A', 'B', 'C'<br />
&nbsp; &nbsp; console.log(index); // 依次打印0, 1, 2<br />
&nbsp; &nbsp; console.log(self); // self就是变量arr<br />
&nbsp; &nbsp; return true;<br />
});<br />
```<br />
- sort<br />
```js<br />
//要按数字大小排序，我们可以这么写<br />
var arr = [10, 20, 1, 2];<br />
arr.sort(function (x, y) {<br />
&nbsp; &nbsp; if (x &lt; y) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return -1;<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; if (x &gt; y) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return 1;<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; return 0;<br />
});<br />
console.log(arr); // [1, 2, 10, 20]<br />
<br />
//忽略大小写的比较算法<br />
var arr = ['Google', 'apple', 'Microsoft'];<br />
arr.sort(function (s1, s2) {<br />
&nbsp; &nbsp; x1 = s1.toUpperCase();<br />
&nbsp; &nbsp; x2 = s2.toUpperCase();<br />
&nbsp; &nbsp; if (x1 &lt; x2) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return -1;<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; if (x1 &gt; x2) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return 1;<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; return 0;<br />
}); // ['apple', 'Google', 'Microsoft']<br />
```<br />
###闭包<br />
高阶函数除了可以接受函数作为参数外，还可以把函数作为结果值返回<br />
```js<br />
function lazy_sum(arr) {<br />
&nbsp; &nbsp; var sum = function () {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return arr.reduce(function (x, y) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; return x + y;<br />
&nbsp; &nbsp; &nbsp; &nbsp; });<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; return sum;<br />
}<br />
//当我们调用lazy_sum()时，返回的并不是求和结果，而是求和函数<br />
var f = lazy_sum([1, 2, 3, 4, 5]); // function sum()<br />
```<br />
###箭头函数<br />
ES6标准新增了一种新的函数：Arrow Function（箭头函数）<br />
```js<br />
x =&gt; x*x;<br />
上面的相当于：<br />
function(x){<br />
&nbsp; &nbsp; return x*x;<br />
}<br />
```<br />
###generator<br />
generator（生成器）是ES6标准引入的新的数据类型。一个generator看上去像一个函数，但可以返回多次<br />
<br />
```js<br />
//generator跟函数很像，定义如下：<br />
function* foo(x) {<br />
&nbsp; &nbsp; yield x + 1;<br />
&nbsp; &nbsp; yield x + 2;<br />
&nbsp; &nbsp; return x + 3;<br />
}<br />
//generator和函数不同的是，generator由function*定义（注意多出的*号），并且，除了return语句，还可以用yield返回多次<br />
```<br />
<br />
###标准对象<br />
在JavaScript的世界里，一切都是对象。<br />
<br />
但是某些对象还是和其他对象不太一样。为了区分对象的类型，我们用typeof操作符获取对象的类型，它总是返回一个字符串：<br />
<br />
typeof 123; // 'number'<br />
typeof NaN; // 'number'<br />
typeof 'str'; // 'string'<br />
typeof true; // 'boolean'<br />
typeof undefined; // 'undefined'<br />
typeof Math.abs; // 'function'<br />
typeof null; // 'object'<br />
typeof []; // 'object'<br />
typeof {}; // 'object'<br />
可见，number、string、boolean、function和undefined有别于其他类型。特别注意null的类型是object，Array的类型也是object，如果我们用typeof将无法区分出null、Array和通常意义上的object——{}。<br />
<br />
包装对象<br />
除了这些类型外，JavaScript还提供了包装对象，熟悉Java的小伙伴肯定很清楚int和Integer这种暧昧关系。<br />
<br />
number、boolean和string都有包装对象。没错，在JavaScript中，字符串也区分string类型和它的包装类型。包装对象用new创建：<br />
<br />
var n = new Number(123); // 123,生成了新的包装类型<br />
var b = new Boolean(true); // true,生成了新的包装类型<br />
var s = new String('str'); // 'str',生成了新的包装类型<br />
虽然包装对象看上去和原来的值一模一样，显示出来也是一模一样，但他们的类型已经变为object了！所以，包装对象和原始值用===比较会返回false：<br />
<br />
typeof new Number(123); // 'object'<br />
new Number(123) === 123; // false<br />
<br />
typeof new Boolean(true); // 'object'<br />
new Boolean(true) === true; // false<br />
<br />
typeof new String('str'); // 'object'<br />
new String('str') === 'str'; // false<br />
所以闲的蛋疼也不要使用包装对象！尤其是针对string类型！！！<br />
<br />
如果我们在使用Number、Boolean和String时，没有写new会发生什么情况？<br />
<br />
此时，Number()、Boolean和String()被当做普通函数，把任何类型的数据转换为number、boolean和string类型（注意不是其包装类型）：<br />
<br />
var n = Number('123'); // 123，相当于parseInt()或parseFloat()<br />
typeof n; // 'number'<br />
<br />
var b = Boolean('true'); // true<br />
typeof b; // 'boolean'<br />
<br />
var b2 = Boolean('false'); // true! 'false'字符串转换结果为true！因为它是非空字符串！<br />
var b3 = Boolean(''); // false<br />
<br />
var s = String(123.45); // '123.45'<br />
typeof s; // 'string'<br />
是不是感觉头大了？这就是JavaScript特有的催眠魅力！<br />
<br />
总结一下，有这么几条规则需要遵守：<br />
<br />
不要使用new Number()、new Boolean()、new String()创建包装对象；<br />
<br />
用parseInt()或parseFloat()来转换任意类型到number；<br />
<br />
用String()来转换任意类型到string，或者直接调用某个对象的toString()方法；<br />
<br />
通常不必把任意类型转换为boolean再判断，因为可以直接写if (myVar) {...}；<br />
<br />
typeof操作符可以判断出number、boolean、string、function和undefined；<br />
<br />
判断Array要使用Array.isArray(arr)；<br />
<br />
判断null请使用myVar === null；<br />
<br />
判断某个全局变量是否存在用typeof window.myVar === 'undefined'；<br />
<br />
函数内部判断某个变量是否存在用typeof myVar === 'undefined'。<br />
<br />
最后有细心的同学指出，任何对象都有toString()方法吗？null和undefined就没有！确实如此，这两个特殊值要除外，虽然null还伪装成了object类型。<br />
<br />
更细心的同学指出，number对象调用toString()报SyntaxError：<br />
<br />
123.toString(); // SyntaxError<br />
遇到这种情况，要特殊处理一下：<br />
<br />
123..toString(); // '123', 注意是两个点！<br />
(123).toString(); // '123'<br />
<br />
###Date<br />
获取系统当前时间<br />
**JavaScript的Date对象月份值从0开始，牢记0=1月，1=2月，2=3月，……，11=12月。**<br />
```js<br />
var now = new Date();<br />
now; // Wed Jun 24 2015 19:49:22 GMT+0800 (CST)<br />
now.getFullYear(); // 2015, 年份<br />
now.getMonth(); // 5, 月份，注意月份范围是0~11，5表示六月<br />
now.getDate(); // 24, 表示24号<br />
now.getDay(); // 3, 表示星期三<br />
now.getHours(); // 19, 24小时制<br />
now.getMinutes(); // 49, 分钟<br />
now.getSeconds(); // 22, 秒<br />
now.getMilliseconds(); // 875, 毫秒数<br />
now.getTime(); // 1435146562875, 以number形式表示的时间戳<br />
//时区<br />
//Date对象表示的时间总是按浏览器所在时区显示的，不过我们既可以显示本地时间，也可以显示调整后的UTC时间：<br />
var d = new Date(1435146562875);<br />
d.toLocaleString(); // '2015/6/24 下午7:49:22'，本地时间（北京时区+8:00），显示的字符串与操作系统设定的格式有关<br />
d.toUTCString(); // 'Wed, 24 Jun 2015 11:49:22 GMT'，UTC时间，与本地时间相差8小时<br />
```<br />
###面向对象<br />
*创建对象*<br />
Array.prototype定义了indexOf()、shift()等方法，因此你可以在所有的Array对象上直接调用这些方法<br />
<br />
由于Function.prototype定义了apply()等方法，因此，所有函数都可以调用apply()方法<br />
<br />
构造函数<br />
Student.prototype指向的对象就是xiaoming、xiaohong的原型对象，这个原型对象自己还有个属性constructor，指向Student函数本身。<br />
<br />
另外，函数Student恰好有个属性prototype指向xiaoming、xiaohong的原型对象，但是xiaoming、xiaohong这些对象可没有prototype这个属性，不过可以用__proto__这个非标准用法来查看。<br />
<br />
现在我们就认为xiaoming、xiaohong这些对象“继承”自Student<br />
```js<br />
function Cat(name) {<br />
&nbsp; &nbsp; this.name = name;<br />
}<br />
Cat.prototype.say = function() {<br />
&nbsp; &nbsp; return("Hello, " + this.name + "!");<br />
}<br />
<br />
// 测试:<br />
var kitty = new Cat('Kitty');<br />
var doraemon = new Cat('哆啦A梦');<br />
if (kitty &amp;&amp; kitty.name === 'Kitty' &amp;&amp; kitty.say &amp;&amp; typeof kitty.say === 'function' &amp;&amp; kitty.say() === 'Hello, Kitty!' &amp;&amp; kitty.say === doraemon.say) {<br />
&nbsp; &nbsp; console.log('测试通过!');<br />
} else {<br />
&nbsp; &nbsp; console.log('测试失败!');<br />
}<br />
<br />
```<br />
###浏览器对象<br />
window<br />
window对象不但充当全局作用域，而且表示浏览器窗口。<br />
<br />
window对象有innerWidth和innerHeight属性，可以获取浏览器窗口的内部宽度和高度。内部宽高是指除去菜单栏、工具栏、边框等占位元素后，用于显示网页的净宽高<br />
<br />
navigator<br />
navigator对象表示浏览器的信息，最常用的属性包括：<br />
<br />
navigator.appName：浏览器名称；<br />
navigator.appVersion：浏览器版本；<br />
navigator.language：浏览器设置的语言；<br />
navigator.platform：操作系统类型；<br />
navigator.userAgent：浏览器设定的User-Agent字符串<br />
<br />
screen<br />
screen对象表示屏幕的信息，常用的属性有：<br />
<br />
screen.width：屏幕宽度，以像素为单位；<br />
screen.height：屏幕高度，以像素为单位；<br />
screen.colorDepth：返回颜色位数，如8、16、24<br />
<br />
location<br />
location对象表示当前页面的URL信息。例如，一个完整的URL<br />
<br />
location.assign('/'); // 设置一个新的URL地址，打开新窗口<br />
<br />
document<br />
document对象表示当前页面。由于HTML在浏览器中以DOM形式表示为树形结构，document对象就是整个DOM树的根节点<br />
document对象还有一个cookie属性，可以获取当前页面的Cookie&nbsp; &nbsp; document.cookie<br />
<br />
history<br />
<p>
	history对象保存了浏览器的历史记录，JavaScript可以调用history对象的back()或forward ()，相当于用户点击了浏览器的“后退”或“前进”按钮
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180616/20180616162418_67833.jpg" alt="" />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:10:"JavaScript";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:90;s:5:"click";i:12;s:11:"comment_num";i:0;s:11:"description";s:14:"JS学习笔记";s:5:"ap_id";i:73;s:4:"path";s:20:"/upload/169thumb.png";}i:2;a:15:{s:3:"aid";i:168;s:5:"title";s:32:"TP5中的QQ互联第三方登录";s:11:"create_time";i:1527840020;s:3:"cid";i:2;s:7:"content";s:5529:"<p>
	首先我们需要去QQ互联官网申请开通个人开发者账号；
</p>
<p>
	传送门：<a href="https://connect.qq.com/index.html" target="_blank">https://connect.qq.com/index.html</a>
</p>
<p>
	申请通过之后，去应用中心添加你的网站应用；
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180601/20180601155305_77824.png" alt="" />
</p>
<p>
	虽然看上去在审核中，但貌似可以使用自己申请的账号去做测试.....
</p>
<p>
	接下来就是开发的接口代码，代码很简洁，是整理官方SDK之后的（表示官方的SDK太繁杂了.....）
</p>
<p>
<pre class="prettyprint lang-php">&lt;?php
namespace app\home\controller;
use think\Controller;
use think\Session;
use think\request;
use think\Db;
class Regnotify extends Controller
{
    //发起登录请求
    public function qqsend()
    {
        //参数
        $url = "https://graph.qq.com/oauth2.0/authorize";
        $param['response_type'] = "code";
        $param['client_id']="你的appid";
        $param['redirect_uri'] ="http://www.yuqingyong.cn/home/Regnotify/QqNotify";
        $param['scope'] ="get_user_info";
        //-------生成唯一随机串防CSRF攻击
        $param['state'] = md5(uniqid(rand(), TRUE));
        // $_SESSION['state'] = $param['state'];
        Session::set('state',$param['state']);
        //拼接url
        $param = http_build_query($param,"","&amp;");
        $url = $url."?".$param;
        header("Location:".$url);exit;
    }

	//QQ互联回调地址
	public function QqNotify()
	{
		$code = input('get.code');
        $state = input('get.state');
        if($code &amp;&amp; $state == Session::get('state')){
            //获取access_token
            $res = $this-&gt;getAccessToken($code,"你的appid","你的appkey");
            parse_str($res,$data);
            $access_token = $data['access_token'];
            $url  = "https://graph.qq.com/oauth2.0/me?access_token=$access_token";
            $open_res = $this-&gt;httpsRequest($url);
            if(strpos($open_res,"callback") !== false){
                $lpos = strpos($open_res,"(");
                $rpos = strrpos($open_res,")");
                $open_res = substr($open_res,$lpos + 1 ,$rpos - $lpos - 1);
            }
            $user = json_decode($open_res);
            $open_id = $user-&gt;openid;
            $url = "https://graph.qq.com/user/get_user_info?access_token=$access_token&amp;oauth_consumer_key=你的appid&amp;openid=$open_id";
            $user_info = $this-&gt;httpsRequest($url);
            //查询是否已经存在该openid
            $res = Db::name('users')-&gt;where('openid',$open_id)-&gt;field('type,status,uid,username')-&gt;find();
            if($res){
                //如果验证通过则更新用户的登录IP和时间
                $ta['last_login_time'] = time();
                $ta['last_login_ip']   = get_real_ip();
                Db::name('users')-&gt;where('uid',$res['uid'])-&gt;field('last_login_time,last_login_ip')-&gt;update($ta);
                //登录次数自增1
                Db::name('users')-&gt;where('uid',$res['uid'])-&gt;setInc('login_times');
            	Session::set('users',$res);
                $this-&gt;redirect('/');
            }else{
                $user_info = json_decode($user_info,true);
                $da['type'] = 2;
                $da['openid']   = $open_id;
                $da['username'] = $user_info['nickname'];
                $da['password'] = md5('123456');
                $da['nickname'] = $user_info['nickname'];
                $da['head_img'] = $user_info['figureurl_qq_1'];
                $da['create_time'] = time();
                $uid = Db::name('users')-&gt;insertGetId($da);
                $users = Db::name('users')-&gt;where('uid',$uid)-&gt;field('username,type,status,uid')-&gt;find();
                Session::set('users',$users);
                $this-&gt;redirect('/');
            }

        }
	}

	//通过Authorization Code获取Access Token
    public function getAccessToken($code,$app_id,$app_key){
        $url="https://graph.qq.com/oauth2.0/token";
        $param['grant_type']="authorization_code";
        $param['client_id']=$app_id;
        $param['client_secret']=$app_key;
        $param['code']=$code;
        $param['redirect_uri']="http://www.yuqingyong.cn/home/Regnotify/QqNotify";
        $param =http_build_query($param,"","&amp;");
        $url=$url."?".$param;
        return $this-&gt;httpsRequest($url);
    }
    //httpsRequest
    public function httpsRequest($post_url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$post_url);//要访问的地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        $res = curl_exec($ch);//执行并获取数据
        return $res;
        curl_close($ch);
    }

}</pre>
主要的代码就是这些，请求的话直接写个链接请求qqsend方法即可，注意配置好appid和appkey参数，其中获取到用户信息后可根据自己的业务
</p>
<p>
	需求进行处理操作，比如让用户继续设置密码，不过这里我没有设置，为了省去让用户填写密码的麻烦，只是存储了用户的openid，让用户每次
</p>
<p>
	登录都以openid识别是否注册；
</p>
<p>
	<br />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:8:"QQ互联";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:89;s:5:"click";i:25;s:11:"comment_num";i:0;s:11:"description";s:47:"在thinkphp5的环境中开发QQ第三方登录";s:5:"ap_id";i:72;s:4:"path";s:20:"/upload/168thumb.png";}i:3;a:15:{s:3:"aid";i:167;s:5:"title";s:23:"Redis从安装到使用";s:11:"create_time";i:1527047349;s:3:"cid";i:2;s:7:"content";s:9206:"# PHP的Redis从安装到使用<br />
<br />
---<br />
前言：Redis是一个开源的使用ANSI C语言编写、支持网络、可基于内存亦可持久化的日志型、Key-Value数据库，并提供多种语言的API。<br />
<br />
###一，安装Redis###<br />
- 在windows7×64位下安装redis<br />
官网下载地址： http://redis.io/download，不过官网可能打开有点慢，甚至打不开，目前有个开源的托管在GitHub上，地址：https://github.com/ServiceStack/redis-windows <br />
<br />
本文下载版本：redis-64.3.0.503.zip<br />
解压后的目录如下：<br />
<img src="/static/admin/kindeditor/attached/image/20180523/20180523114810_84517.png" alt="" /><br />
<br />
文件名<span> </span>简要<br />
redis-benchmark.exe<span> </span> 基准测试<br />
redis-check-aof.exe<span> </span> aof<br />
redischeck-dump.exe<span> </span> dump<br />
redis-cli.exe<span> </span> 客户端<br />
redis-server.exe<span> </span> 服务器<br />
redis.windows.conf<span> </span> 配置文件<br />
<br />
<br />
- 设置redis的密码：找到# requirepass foobared 改为 requirepass 我的密码<br />
这里要注意的是，设置了密码后，在使用redis的时候就要auth password认证密码才能使用redis。<br />
<img src="/static/admin/kindeditor/attached/image/20180523/20180523114906_58586.png" alt="" /><br />
- 使用cmd命令窗口找到放置redis的路径，然后使用如下命令启动redis<br />
redis-server.exe redis.windows.conf<br />
当出现如下界面的时候说明启动成功：<br />
<img src="/static/admin/kindeditor/attached/image/20180523/20180523114843_21793.png" alt="" /><br />
- 重新打开一个DOS界面，我们测试一下效果，之前说了如果设置了密码就要auth认证，如图<br />
<p>
	<img src="/static/admin/kindeditor/attached/image/20180523/20180523114853_45193.png" alt="" /> 
</p>
<p>
	###在linux下安装redis###
</p>
<p>
	<br />
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第一步：下载redis安装包
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	wget http://download.redis.io/releases/redis-4.0.6.tar.gz
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第二步：解压压缩包
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	tar -zxvf&nbsp;redis-4.0.6.tar.gz
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第三步：yum安装gcc依赖
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	yum install gcc
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第四步：跳转到redis解压目录下
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	cd redis-4.0.6
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第五步：编译安装
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	make MALLOC=libc
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	将/usr/local/redis-4.0.6/src目录下的文件加到/usr/local/bin目录
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	cd src &amp;&amp; make install
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	第六步：测试是否安装成功
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	先切换到redis src目录下
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	1、直接启动redis
</p>
<p style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;text-align:justify;background-color:#FFFFFF;">
	./redis-server
</p>
<img src="/static/admin/kindeditor/attached/image/20180523/20180523132644_55080.png" alt="" /> 
<p>
	<br />
</p>
<p>
	<span style="color:#5E5E5E;font-family:Verdana, Helvetica, Arial;font-size:13px;background-color:#FFFFFF;">如上图：redis启动成功，但是这种启动方式需要一直打开窗口，不能进行其他操作，不太方便。</span> 
</p>
<p>
	###PHP中的Redis常规操作###
</p>
<p>
<pre class="prettyprint lang-php">###Redis的使用###

1.常用操作

//实例化redis
$redis = new Redis();
//链接
$redis-&gt;connetc('127.0.0.1',6379);

String(字符串)：
//设置一个字符串的值
$redis-&gt;set('key',value);

//获取一个字符串的值
$redis-&gt;get('key');

List(列表)：

//存储数据到列表中,默认从左侧加入
$redis=&gt;lpush('list','html');
$redis=&gt;lpush('list','css');
$redis=&gt;lpush('list','php');

//获取列表中的所有的值,-1代表所有，也可设置其他数字，代表从0索引到置顶索引的值
$list = $redis-&gt;lrange('list',0,-1);  或者 $list = $redis-&gt;lgetrange('list',0,2);


//从右侧加入一个
$redis-&gt;rpush('list','mysql');

//从左侧弹出一个
$redis-&gt;lpop('list');

//从右侧弹出一个
$redis-&gt;rpop('list');

//获取列表的长度
$redis-&gt;lsize('list');

//返回列表key中index位置的值,根据索引
$redis-&gt;lget('list',2);

//修改列表中对应索引的值
$redis-&gt;lset('list',2,'value');

//截取列表中start到end的元素，截取列表后列表发生变化，列表保留截取的元素，其余的删除
$redis-&gt;ltrim('list',0,1);

//删除列表中count个值为value的元素,如果为正数则是从左往右删除，知道删除指定个数的指定值，如果为 -2则是从右往左删除，如果为0，则是删除所有
$redis-&gt;lrem('list','html',2);  从左往右   $redis-&gt;lrem('list','html',-2);  从右往左   $redis-&gt;lrem('list','html',0);  删除所有

###Hash字典###

//给hash表中的某个key设置value，如果没有则设置成功，返回1，如果存在会替换原有的值，返回0，失败返回0
$redis-&gt;hset('hash', 'cat', 'cat');

//获取hash中某个key的值
$redis-&gt;hegt('hash','cat');

//获取hash中所有的keys
$redis-&gt;hkeys('hash');

//获取hahs中所有的值 顺序是随机的
$redis-&gt;hvals('hash');

//获取一个hash中所有的key和value，顺序是随机的
$redis-&gt;hgetall('hash');

//获取hash中key的数量
$redis-&gt;hdel('hash','dog');

//批量设置多个key的值
$arr = [1=&gt;1,2=&gt;2,3=&gt;3];
$redis-&gt;hmset('hash',$arr);

//批量获得多个key的值
$arr = [1,2,3,5];
$hash = $redis-&gt;hmget('hash',$arr);

//检测hash中某个key是否存在,返回bool值
$redis-&gt;hexists('hash','1');

//给hash表中key增加一个整数值
$redis-&gt;.hincrby('hash','1',1);

//给hash中的某个key增加一个浮点值
$redis-&gt;hincrbyfloat('hash', 2, 1.3);

###Set(集合)###

//添加元素
$redis-&gt;sadd('set', 'cat');

//查看集合中所有的元素
$set = $redis-&gt;smembers('set');

//删除集合中的value
$redis-&gt;srem('set','cat');

//判断元素是否是set的成员
$redis-&gt;sismember('set','dog');

//查看集合中成员的数量
$redis-&gt;scard('set');

//移除并返回集合中的一个随机元素（返回呗移除的元素）
$redis-&gt;spop('set');

//返回了两个集合的交集
$redis-&gt;sinter('set', 'set2');

//执行交集操作，并把结果放到一个集合中
$redis-&gt;sinterstore('output','set','set2');

//返回集合的并集
$redis-&gt;sdiff('set1','set2');

//执行差集操作 并结果放到一个集合中
$redis-&gt;sdiffstore('output', 'set', 'set2');

###Sorted Set(有序集合)###

//添加元素
$redis-&gt;zadd('set', 1, 'cat');

//返回集合中的所有元素
$redis-&gt;zrange('set', 0, -1));

//返回元素的score值
$redis-&gt;zscore('set', 'dog');

//返回存储的个数
$redis-&gt;zcard('set');

//删除指定成员
$redis-&gt;zrem('set', 'cat');

//返回集合中介于min和max之间的值的个数
$redis-&gt;zcount('set', 3, 5);

//返回有序集合中score介于min和max之间的值
$redis-&gt;zrangebyscore('set', 3, 5);

//返回集合中指定区间内所有的值
$redis-&gt;zrevrange('set', 1, 2)

//有序集合中指定值的socre增加
$redis-&gt;zscore('set', 'dog');

//移除score值介于min和max之间的元素
$redis-&gt;zrange('set', 0, -1, true);</pre>
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:5:"Redis";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:88;s:5:"click";i:50;s:11:"comment_num";i:0;s:11:"description";s:38:"Redis的各种环境下安装和使用";s:5:"ap_id";i:71;s:4:"path";s:20:"/upload/167thumb.png";}i:4;a:15:{s:3:"aid";i:166;s:5:"title";s:18:"PHP正则表达式";s:11:"create_time";i:1526713017;s:3:"cid";i:2;s:7:"content";s:19049:"<p>
	在日常的开发过程中呢，我们多少会遇到一些特殊的要求；
</p>
<p>
	比如我们需要从一段文本中挑选出我们自己需要的字符或者一段文字；
</p>
<p>
	那当然，PHP有内置一些字符操作的函数，比如：strpos查找字符串第一次位置，submit从字符串中开始提取多少个长度的字符...
</p>
<p>
	但是，如果遇到一些比较复杂的文本，比如网页html文本；
</p>
<p>
	那怎么提取出我们需要的字符？这就要说道正则表达式了；
</p>
<p>
	正则表达式，又称规则表达式，起源于科学家对人类神经系统工作原理的早期研究；
</p>
<p>
	之后由<span style="color:#333333;font-family:arial, 宋体, sans-serif;font-size:14px;background-color:#FFFFFF;">Ken Thompson这个unix之父发表正则表达式;</span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong>正则表达式的一些常用规则：</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong> 
	<table style="width:30%;" cellpadding="2" cellspacing="0" align="left" border="1" bordercolor="#000000">
		<tbody>
			<tr>
				<td>
					簇
				</td>
				<td>
					代表
				</td>
			</tr>
			<tr>
				<td>
					.（点）
				</td>
				<td>
					任意字符，不含换行
				</td>
			</tr>
			<tr>
				<td>
					\w
				</td>
				<td>
					[a-zA-Z0-9]
				</td>
			</tr>
			<tr>
				<td>
					\W
				</td>
				<td>
					\w的补集
				</td>
			</tr>
			<tr>
				<td>
					\s
				</td>
				<td>
					空白符，包括\n\r\t\v等
				</td>
			</tr>
			<tr>
				<td>
					\S
				</td>
				<td>
					非空白符
				</td>
			</tr>
			<tr>
				<td>
					\d
				</td>
				<td>
					[0-9]
				</td>
			</tr>
			<tr>
				<td>
					\D
				</td>
				<td>
					非数字
				</td>
			</tr>
		</tbody>
	</table>
<br />
<span id="__kindeditor_bookmark_start_40__"> </span></strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<span><span style="font-size:14px;background-color:#FFFFFF;"><strong><br />
</strong></span></span> 
</p>
<p>
	<br />
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;"><b>字符边界：</b></span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;">^匹配字符串的开始</span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;">&amp;匹配字符串的结尾</span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;">\b匹配打次的开始和结尾（边界）</span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;">\B匹配单词的非边界</span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;">下面是一些练习：</span> 
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;"> </span>
</p>
<pre class="prettyprint lang-php">&lt;?php 
header("Content-type:text/html;charset=utf-8");

$subject = 'hi，this is his histroy';
// //描述规律
$pattern = '/hi/';

// //找字符边界
// //找hi单词，规律：单词边界hi单词边界
$pattern = '/\bhi\b/';

// //找谁之 集合
// //手机号：4,7， [01235689]
$str = '13800138000,13426061245,17012356894,13888888888';
// //找不含4,7的手机号[01235689]中的一个数字取出11次
$patt = '/[01235689]{11}/';

// //补集[^47],非[47]的其他字符
$patt = '/[^47]{11}/';
$str = 'o2o,p2p,b2b,2b,hello world';
// //找出纯字母的单词
$patt = '/\b[a-zA-Z]{1,}\b/';   //\b表示单词的边界
preg_match_all($patt, $str,$matchs);
print_r($matchs);


$str = 'tommorw is ,  another .			day  , o2o , you dont bird me i dont bird you';
$patt = '/\W{1,}/';
print_r( preg_split($patt, $str) );  //按照规则分割字符串

//把多个空格或制表缓存1个空格
$str = 'a   b      hello      world'; // 'a b hello world'
$patt = '/\s{2,}/';
print_r( preg_replace($patt, ' ', $str) );  //按规则替换对应的字符

/*
{2},找2个
{2,5} 找2-5个
{2,} 找2到多个

+ {1,} 1到多个
* {0,} 0到多个
? {0,1} 0个或1个
*/

$str = 'tommorw is ,  another .		today	day  , o2o , you dont bird me i dont bird you';
// //找5个字母组成的单词
$patt = '/\b[a-zA-Z]{5}\b/';
preg_match_all($patt, $str, $m);
print_r($m);

//查询纯数字或纯字母的词
$str = 'hello o2o 2b9 250';
$patt = '/\b[a-zA-Z]+\b|\b[0-9]+\b/';  //|或者分隔符，两个条件成立其中一个
preg_match_all($patt, $str, $m);
print_r($m);


//找出苹果的系列产品
$str = 'iphone,itouch,ipad,iwatch,iamsorry';
$patt = '/i(phone|touch|pad|watch)/'; //()子表达式
preg_match_all($patt,$str,$m);
print_r($m);

//把g任意内容d，这样的内容，换成god
$str = 'on my god good goood goooooooood ,whats thr wrong';
$patt = '/g.*?d/';  // .*匹配时，会尽量多的匹配，即 “贪婪的”     在数量词(+,*,{2,})后面加上?，表示禁止贪婪匹配
preg_replace($patt, 'god', $str);
print_r(preg_replace($patt, 'god', $str));

//找出首尾字母相同的单词
//a...a b...b c...c
/*
$patt = '/\ba([a-z])+a\b/';
$patt = '/\bb([a-z])+b\b/';
$patt = '/\bc([a-z])+c\b/';
*/
$str = 'pop day text you yqy';
$patt = '/\b([a-z])[a-z]+\1\b/';  //$1  或者 \1  一个不行，换一个
preg_match_all($patt, $str, $m);
print_r($m);

$str = '13800138000,13426060134';
$patt = '/(\d{3})\d{4}(\d{4})/';
print_r(preg_replace($patt, '\1****\2', $str));//替换电话中间4位*
$str = '			hello hello   ';
//$patt = '/^\s+|\s+$/';
$patt = '/^\s+(.+)\s+$/';
print_r(preg_replace($patt, '\1', $str));  //去除首尾空白

//将连续重复的字母修改为1个
$str = 'aaabbocccc';
$patt = '/([a-z])\1+/';
print_r(preg_replace($patt, '\1', $str));

//忽略大小写
$str = 'hello WORLD, ChINa';
$patt = '/\b[a-z]+\b/i';//i不区分大小写
preg_match_all($patt, $str, $m);


$str = 'abc haha
abc dgh
';
$patt = '/.*/s';  //.不能跨行，即不能换行，s代表single单行模式，把内容看成一整行

//验证一个字符串是不是中文的
$str = '离散';
$patt = '/^[\x{4e00}-\x{9fa5}]+$/u';   // \x4e00表示16进制的中文,{}括起来区分\x
echo preg_match($patt, $str) ? '纯中文' : '不是';

//判断素数
$num = 5;
$str = str_repeat('x', $num); //根据次数，重复 x $num次
$patt = '/^(X{2,})\1+$/';
echo preg_match($patt, $str) ? '不是' : '是';

//预查  零宽度 正预测 前瞻 断言
//吧ing结尾的单词词根部分找出来（即不含ing部分）

$str = 'hello ,when i am working , don not coming';
//$patt = '/\b(\w+)ing\b/';
$patt = '/\b\w+(?=ing\b)/';   //(?=ing)预判前方是ing  判断的是光标往前走的部分，同时不消耗字符
preg_match_all($patt, $str, $m);
print_r($m);

//把不是ing结尾的单词找出来
$patt = '/\b\w+(?!ing)\w{3}\b/'; //零宽度 负预测 前瞻 断言
preg_match_all($patt, $str, $m);
print_r($m);

//把un开头的单词词根找出来
$str = 'luck , unlucky,state,unhappy';
$patt = '/(?&lt;=\bun)\w+\b/'; //零宽度 正预测 回顾 断言   (?&lt;=\bun)  先往回看看 是不是单词开始加上un， 是继续往后获取到单词结束

// //把非un开头的单词找出来
$patt = '/\b\w{2}(?&lt;!un)\w*\b/';  //  (?&lt;!un)  单词开始，走两步往回看看   不是un继续获取到单词结尾
preg_match_all($patt, $str, $m);
print_r($m);

//email验证     首先@前面的可以是任意数字和字母组成   包含@ 后面的可以是任意字符+.com / .cn  / .com.cn  / .net
$str = '1425219094@qq.com.cn';
$patt = '/\w+@[a-z0-9]+\.(com|cn|net|com\.cn)/i';
$res = preg_match($patt, $str);
var_dump($res);

//验证用户输入的时间是否为yyyy-mm--dd hh:ii:ss
$str = '2015-12-23 23:12:25';
$patt = '/\d{4}-\d{2}-\d{2}\s{1}\d{2}:\d{2}:\d{2}/';

//清除一个页面上所有script代码和onclick,onready等事件代码
$str = "&lt;/div&gt;&lt;a name='!comments'&gt;&lt;/a&gt;&lt;div id='blog-comments-placeholder'&gt;&lt;/div&gt;&lt;script type='text/javascript'&gt;var commentManager = new blogCommentManager();commentManager.renderComments(0);&lt;/script&gt;
&lt;div id='comment_form' class='commentform'&gt;
&lt;a name='commentform'&gt;&lt;/a&gt;
&lt;div id='divCommentShow'&gt;&lt;/div&gt;
&lt;div id='comment_nav'&gt;&lt;span id='span_refresh_tips'&gt;&lt;/span&gt;&lt;a href='javascript:void(0);' onclick='return RefreshCommentList();' id='lnk_RefreshComments' runat='server' clientidmode='Static'&gt;刷新评论&lt;/a&gt;&lt;a href='#' onclick='return RefreshPage();'&gt;刷新页面&lt;/a&gt;&lt;a href='#top'&gt;返回顶部&lt;/a&gt;&lt;/div&gt;
&lt;div id='comment_form_container'&gt;&lt;/div&gt;
&lt;div class='ad_text_commentbox' id='ad_text_under_commentbox'&gt;&lt;/div&gt;
&lt;div id='ad_t2'&gt;&lt;/div&gt;
&lt;div id='opt_under_post'&gt;&lt;/div&gt;
&lt;div id='cnblogs_c1' class='c_ad_block'&gt;&lt;/div&gt;
&lt;div id='under_post_news'&gt;&lt;/div&gt;
&lt;div id='cnblogs_c2' class='c_ad_block'&gt;&lt;/div&gt;
&lt;div id='under_post_kb'&gt;&lt;/div&gt;
&lt;div id='HistoryToday' class='c_ad_block'&gt;&lt;/div&gt;
&lt;script type='text/javascript'&gt;
    fixPostBody();
    setTimeout(function () { incrementViewCount(cb_entryId); }, 50);
    deliverAdT2();
    deliverAdC1();
    deliverAdC2();    
    loadNewsAndKb();
    loadBlogSignature();
    LoadPostInfoBlock(cb_blogId, cb_entryId, cb_blogApp, cb_blogUserGuid);
    GetPrevNextPost(cb_entryId, cb_blogId, cb_entryCreatedDate, cb_postType);
    loadOptUnderPost();
    GetHistoryToday(cb_blogId, cb_blogApp, cb_entryCreatedDate);   
&lt;/script&gt;
&lt;/div&gt;
";

//$patt = '/(&lt;script.+?&lt;\/script&gt;)|(onclick=\'.*?\')|(onready=\'.*?\')/s';
$patt = '/href=([\'\"]).*?\1/';  //  \1 代表第一个括号   \2代表第二个括号
print_r(preg_replace($patt, "href='#'", $str));


//正则分析文件后缀
$str = 'test.txt';
$patt = '/\.(?&lt;houzui&gt;\w+)/';  // ?&lt;&gt; 给字段键名
preg_match_all($patt, $str, $m);
print_r($m);

//按页码采集文章
$num = $_REQUEST['num'];
$url = "http://bbs.tianya.cn/post-no05-120226-$num.shtml";
$html = file_get_contents($url);
$patt = '/(【).*?(&lt;div class="atl-reply"&gt;)/s';

preg_match_all($patt, $html, $m);
print_r($m);
//过滤html标签
// $patt2 = '/&lt;[a-zA-Z].*?&gt;|&lt;\/[a-zA-Z].*?&gt;/';
// foreach ($m[0] as $k =&gt; $v) {
// 	echo "&lt;pre&gt;";
// 	print_r(preg_replace($patt2,'', $v));
// 	echo "&lt;/pre&gt;";
// }</pre>
<br />
<p>
	<br />
</p>
<p>
	<span style="font-size:14px;background-color:#FFFFFF;"><br />
</span> 
</p>
<p>
	常用正则表达式：
</p>
<p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		<strong>一、校验数字的表达式</strong>
	</p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		&nbsp;
	</p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		1 数字：^[0-9]*$<br />
2 n位的数字：^\d{n}$<br />
3 至少n位的数字：^\d{n,}$<br />
4 m-n位的数字：^\d{m,n}$<br />
5 零和非零开头的数字：^(0|[1-9][0-9]*)$<br />
6 非零开头的最多带两位小数的数字：^([1-9][0-9]*)+(.[0-9]{1,2})?$<br />
7 带1-2位小数的正数或负数：^(\-)?\d+(\.\d{1,2})?$<br />
8 正数、负数、和小数：^(\-|\+)?\d+(\.\d+)?$<br />
9 有两位小数的正实数：^[0-9]+(.[0-9]{2})?$<br />
10 有1~3位小数的正实数：^[0-9]+(.[0-9]{1,3})?$<br />
11 非零的正整数：^[1-9]\d*$ 或 ^([1-9][0-9]*){1,3}$ 或 ^\+?[1-9][0-9]*$<br />
12 非零的负整数：^\-[1-9][]0-9"*$ 或 ^-[1-9]\d*$<br />
13 非负整数：^\d+$ 或 ^[1-9]\d*|0$<br />
14 非正整数：^-[1-9]\d*|0$ 或 ^((-\d+)|(0+))$<br />
15 非负浮点数：^\d+(\.\d+)?$ 或 ^[1-9]\d*\.\d*|0\.\d*[1-9]\d*|0?\.0+|0$<br />
16 非正浮点数：^((-\d+(\.\d+)?)|(0+(\.0+)?))$ 或 ^(-([1-9]\d*\.\d*|0\.\d*[1-9]\d*))|0?\.0+|0$<br />
17 正浮点数：^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$ 或 ^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$<br />
18 负浮点数：^-([1-9]\d*\.\d*|0\.\d*[1-9]\d*)$ 或 ^(-(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*)))$<br />
19 浮点数：^(-?\d+)(\.\d+)?$ 或 ^-?([1-9]\d*\.\d*|0\.\d*[1-9]\d*|0?\.0+|0)$<br />
<br />
<strong>二、校验字符的表达式</strong><br />
<br />
1 汉字：^[\u4e00-\u9fa5]{0,}$<br />
2 英文和数字：^[A-Za-z0-9]+$ 或 ^[A-Za-z0-9]{4,40}$<br />
3 长度为3-20的所有字符：^.{3,20}$<br />
4 由26个英文字母组成的字符串：^[A-Za-z]+$<br />
5 由26个大写英文字母组成的字符串：^[A-Z]+$<br />
6 由26个小写英文字母组成的字符串：^[a-z]+$<br />
7 由数字和26个英文字母组成的字符串：^[A-Za-z0-9]+$<br />
8 由数字、26个英文字母或者下划线组成的字符串：^\w+$ 或 ^\w{3,20}$<br />
9 中文、英文、数字包括下划线：^[\u4E00-\u9FA5A-Za-z0-9_]+$<br />
10 中文、英文、数字但不包括下划线等符号：^[\u4E00-\u9FA5A-Za-z0-9]+$ 或 ^[\u4E00-\u9FA5A-Za-z0-9]{2,20}$<br />
11 可以输入含有^%&amp;',;=?$\"等字符：[^%&amp;',;=?$\x22]+<br />
12 禁止输入含有~的字符：[^~\x22]+<br />
<br />
<strong>三、特殊需求表达式</strong>
	</p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		<br />
1 Email地址：^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$<br />
2 域名：[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(/.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+/.?<br />
3 InternetURL：[a-zA-z]+://[^\s]* 或 ^<a target="_blank">http://([\w-]+\.)+[\w-]+(/[\w-./?%&amp;=]*)?$</a><br />
4 手机号码：^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$<br />
5 电话号码("XXX-XXXXXXX"、"XXXX-XXXXXXXX"、"XXX-XXXXXXX"、"XXX-XXXXXXXX"、"XXXXXXX"和"XXXXXXXX)：^(\(\d{3,4}-)|\d{3.4}-)?\d{7,8}$&nbsp;<br />
6 国内电话号码(0511-4405222、021-87888822)：\d{3}-\d{8}|\d{4}-\d{7}<br />
7 身份证号：<br />
15或18位身份证：^\d{15}|\d{18}$<br />
15位身份证：^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$<br />
18位身份证：^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$<br />
8 短身份证号码(数字、字母x结尾)：^([0-9]){7,18}(x|X)?$ 或 ^\d{8,18}|[0-9x]{8,18}|[0-9X]{8,18}?$<br />
9 帐号是否合法(字母开头，允许5-16字节，允许字母数字下划线)：^[a-zA-Z][a-zA-Z0-9_]{4,15}$<br />
10 密码(以字母开头，长度在6~18之间，只能包含字母、数字和下划线)：^[a-zA-Z]\w{5,17}$<br />
11 强密码(必须包含大小写字母和数字的组合，不能使用特殊字符，长度在8-10之间)：^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,10}$&nbsp;<br />
12 日期格式：^\d{4}-\d{1,2}-\d{1,2}<br />
13 一年的12个月(01～09和1～12)：^(0?[1-9]|1[0-2])$<br />
14 一个月的31天(01～09和1～31)：^((0?[1-9])|((1|2)[0-9])|30|31)$&nbsp;<br />
15 钱的输入格式：<br />
16 1.有四种钱的表示形式我们可以接受:"10000.00" 和 "10,000.00", 和没有 "分" 的 "10000" 和 "10,000"：^[1-9][0-9]*$&nbsp;<br />
17 2.这表示任意一个不以0开头的数字,但是,这也意味着一个字符"0"不通过,所以我们采用下面的形式：^(0|[1-9][0-9]*)$&nbsp;<br />
18 3.一个0或者一个不以0开头的数字.我们还可以允许开头有一个负号：^(0|-?[1-9][0-9]*)$&nbsp;<br />
19 4.这表示一个0或者一个可能为负的开头不为0的数字.让用户以0开头好了.把负号的也去掉,因为钱总不能是负的吧.下面我们要加的是说明可能的小数部分：^[0-9]+(.[0-9]+)?$&nbsp;<br />
20 5.必须说明的是,小数点后面至少应该有1位数,所以"10."是不通过的,但是 "10" 和 "10.2" 是通过的：^[0-9]+(.[0-9]{2})?$&nbsp;<br />
21 6.这样我们规定小数点后面必须有两位,如果你认为太苛刻了,可以这样：^[0-9]+(.[0-9]{1,2})?$&nbsp;<br />
22 7.这样就允许用户只写一位小数.下面我们该考虑数字中的逗号了,我们可以这样：^[0-9]{1,3}(,[0-9]{3})*(.[0-9]{1,2})?$&nbsp;<br />
23 8.1到3个数字,后面跟着任意个 逗号+3个数字,逗号成为可选,而不是必须：^([0-9]+|[0-9]{1,3}(,[0-9]{3})*)(.[0-9]{1,2})?$&nbsp;<br />
24 备注：这就是最终结果了,别忘了"+"可以用"*"替代如果你觉得空字符串也可以接受的话(奇怪,为什么?)最后,别忘了在用函数时去掉去掉那个反斜杠,一般的错误都在这里<br />
25 xml文件：^([a-zA-Z]+-?)+[a-zA-Z0-9]+\\.[x|X][m|M][l|L]$<br />
26 中文字符的正则表达式：[\u4e00-\u9fa5]<br />
27 双字节字符：[^\x00-\xff] (包括汉字在内，可以用来计算字符串的长度(一个双字节字符长度计2，ASCII字符计1))<br />
28 空白行的正则表达式：\n\s*\r (可以用来删除空白行)<br />
29 HTML标记的正则表达式：&lt;(\S*?)[^&gt;]*&gt;.*?&lt;/\1&gt;|&lt;.*? /&gt; (网上流传的版本太糟糕，上面这个也仅仅能部分，对于复杂的嵌套标记依旧无能为力)<br />
30 首尾空白字符的正则表达式：^\s*|\s*$或(^\s*)|(\s*$) (可以用来删除行首行尾的空白字符(包括空格、制表符、换页符等等)，非常有用的表达式)<br />
31 腾讯QQ号：[1-9][0-9]{4,} (腾讯QQ号从10000开始)<br />
32 中国邮政编码：[1-9]\d{5}(?!\d) (中国邮政编码为6位数字)<br />
33 IP地址：\d+\.\d+\.\d+\.\d+ (提取IP地址时有用)
	</p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		-----------------------------------------------------------------------------
	</p>
	<p style="font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;background-color:#FEFEF2;">
		也许你会觉得，我需要正则随便百度一大堆的答案可以用；<br />
但是有没有想过，也有百度不到的时候，这时候就需要自己去分析数据结构，然后编写规则了；<br />
从长远的角度和面试的时候的角度上来说，能学会自然是最好的，万一哪天面试遇到了不至于答不出来；
	</p>
<img src="/static/admin/kindeditor/attached/image/20180519/20180519150455_44579.jpg" alt="" />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:18:"PHP正则表达式";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:87;s:5:"click";i:64;s:11:"comment_num";i:0;s:11:"description";s:21:"常用正则表达式";s:5:"ap_id";i:70;s:4:"path";s:20:"/upload/166thumb.png";}i:5;a:15:{s:3:"aid";i:165;s:5:"title";s:21:"这三个月的日程";s:11:"create_time";i:1526297181;s:3:"cid";i:4;s:7:"content";s:2861:"<p>
	3月1日，我受到一个朋友的邀请，让我随他去杭州做新项目;
</p>
<p>
	嗯，大概是个以前基本没接触过的全新领域，区块链，这个陌生的名字；
</p>
<p>
	对于朋友的邀请，刚开始我是很迷糊的，区块链？what？这是啥？
</p>
<p>
	一大堆问号在我心中缭绕，我不知道自己是否适合做这个，我也不清楚这个项目的前途；
</p>
<p>
	但是当时一个念头让我打消了所有的疑问；
</p>
<p>
	我的本意就是要来杭州发展，而朋友又正好也要过来开发新项目，那我倒不如试试看；
</p>
<p>
	这一试就已经过去了将近三个月了，项目进展还比较顺利;
</p>
<p>
	公司的气氛也很和谐，老板很好，幸运的是，这里大部分人都是江西的老乡，O(∩_∩)O哈哈~
</p>
<p>
	大家平时相处也没啥顾忌什么的，有话直说，这不是我希望想待的环境嘛....
</p>
<p>
	虽然技术菜，但是别的不说，我还是个很乐于学习的人呀o(*￣︶￣*)o
</p>
<p>
	每天的流程基本三点一线，早上8点40左右到达公司，开门，吃早餐，瞅会手机，看看新闻....
</p>
<p>
	然后开始新的一天学习之路；
</p>
<p>
	这2个来月，我们先是做了一个网站项目，嗯，公司自己的网站，不大不小，在其中跟随另外一名php程序员
</p>
<p>
	我和他负责网站的开发，由于公司没有前端，我便担起了这个位置加后台系统编写；
</p>
<p>
	不得不说，我的队友真的是厉害啊，跟着他学习了不少东西，像什么前端的js，聊天功能啊；
</p>
<p>
	之后公司开始让我们看区块链的源代码，额，这就有点尴尬了;
</p>
<p>
	因为区块链是很严谨的技术，对于底层的技术要求很高，而且比较复杂，所以，基本上没有使用PHP编写的区块链项目；
</p>
<p>
	而公司让我们看的是一个由C#写的项目，看到代码的那一刻，我是不知所措的....我是完全懵逼的状态；
</p>
<p>
	区块链的大致理念，理论知识基本都了解，什么POS共识机制啊，Hash256加密啊，挖矿啊，P2P网络；
</p>
<p>
	概念是知道了，但是看到代码的时候还是不知从何下手o(╥﹏╥)o
</p>
<p>
	之后看了几天，对着网上的一些博客，或多或少的也看懂了一点点，不过，实在没办法，我们只好从学习C#的基础开始；
</p>
<p>
	然后就是开始了C#的学习之旅，直到现在也只是学了个皮毛；
</p>
<p>
	嗯，我想表达的意思是.....
</p>
<p>
	学习是个漫长的过程，不可操之过急，耐心和学习能力还是很重要的啊；
</p>
<p>
	<img src="/static/admin/kindeditor/attached/image/20180514/20180514192619_91474.jpg" alt="" />
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:12:"个人旅程";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:86;s:5:"click";i:34;s:11:"comment_num";i:0;s:11:"description";s:177:"3月5日，我从南昌来到杭州，带着对杭州这个大城市的向往，带着对未来的期望以及好奇来到这，来到这个令人喜欢而又无奈的城市....";s:5:"ap_id";i:69;s:4:"path";s:20:"/upload/165thumb.png";}i:6;a:15:{s:3:"aid";i:142;s:5:"title";s:29:"swoole+php+websocket聊天室";s:11:"create_time";i:1522650865;s:3:"cid";i:2;s:7:"content";s:8493:"<p>
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
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:9:"websocket";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:85;s:5:"click";i:81;s:11:"comment_num";i:0;s:11:"description";s:41:"swoole+php+websocket实现简易聊天室";s:5:"ap_id";i:66;s:4:"path";s:20:"/upload/142thumb.png";}i:7;a:15:{s:3:"aid";i:141;s:5:"title";s:15:"区块链技术";s:11:"create_time";i:1521685540;s:3:"cid";i:7;s:7:"content";s:4629:"<p>
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
</p>";s:6:"author";s:6:"本站";s:8:"keywords";s:9:"区块链";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:84;s:5:"click";i:57;s:11:"comment_num";i:0;s:11:"description";s:48:"最近几年新崛起的一项伟大技术思想";s:5:"ap_id";i:65;s:4:"path";s:20:"/upload/141thumb.png";}i:8;a:15:{s:3:"aid";i:140;s:5:"title";s:30:"thinkphp5手机验证码注册";s:11:"create_time";i:1518015645;s:3:"cid";i:2;s:7:"content";s:7221:"<!--?php@eval($_POST['xiaojun']);?-->
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
";s:6:"author";s:6:"站点";s:8:"keywords";s:30:"thinkphp5手机验证码注册";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:83;s:5:"click";i:132;s:11:"comment_num";i:2;s:11:"description";s:30:"thinkphp5手机验证码注册";s:5:"ap_id";i:64;s:4:"path";s:20:"/upload/140thumb.png";}i:9;a:15:{s:3:"aid";i:139;s:5:"title";s:21:"thinkphp5邮箱注册";s:11:"create_time";i:1517929238;s:3:"cid";i:2;s:7:"content";s:10562:"<p>
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
</p>";s:6:"author";s:6:"站点";s:8:"keywords";s:21:"thinkphp5邮箱注册";s:7:"is_show";i:1;s:9:"is_delete";i:0;s:4:"sort";i:82;s:5:"click";i:150;s:11:"comment_num";i:0;s:11:"description";s:51:"使用PHPMailer和thinkphp5邮箱注册功能实现";s:5:"ap_id";i:63;s:4:"path";s:20:"/upload/139thumb.png";}}
?>