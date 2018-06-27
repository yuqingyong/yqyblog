<?php
namespace wxpay;
use think\Controller;
use think\request;
/**
 * @Author: 小尤
 * @Date:   2017-08-30
 * @note:   微信支付/退款
 * @from:   CSDN博客(江南极客:http://blog.csdn.net/sinat_35861727?viewmode=contents)
 */

class WxPay{

	/**
     * 默认支付参数配置,可以在这里配置,也可以在初始化的时候,统一传入参数
     * @var array
     */
    private $config = array(
        'appid'			=> '******************',
		'mch_id'	 	=> '***********',
		'pay_apikey' 	=> '***********',
		'api_cert'		=> '/cert/apiclient_cert.pem',	
		'api_key'		=> '/cert/apiclient_key.pem'
    );
	
	public function __construct($config = array()){
		$this->config   =   array_merge($this->config,$config);
	}
	
	/**
     * 使用 $this->name=$value 	配置参数
     * @param  string $name 	配置名称
     * @param  string $value    配置值
     */
	public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
	
	/**
     * 使用 $this->name 获取配置
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name) {
        return $this->config[$name];
    }
	
	public function __isset($name){
        return isset($this->config[$name]);
    }
	
	//----------------------------------------------------------重点看这里---------------------------------------------------------
	/**
     * 微信支付请求接口(POST)
     * @param string $openid 	openid
     * @param string $body 		商品简单描述
     * @param string $order_sn  订单编号
     * @param string $total_fee 金额
     * @return  json的数据
     */
	public function wxpay($openid,$total_fee,$body,$order_sn){
		$config = $this->config;
		
		//统一下单参数构造
		$unifiedorder = array(
			'appid'			=> $config['appid'],
			'mch_id'		=> $config['mch_id'],
			'nonce_str'		=> self::getNonceStr(),
			'body'			=> $body,
			'out_trade_no'	=> $order_sn,
			'total_fee'		=> $total_fee * 100,
			'spbill_create_ip'	=> self::getip(),
			'notify_url'	=> 'http://'.$_SERVER['HTTP_HOST'].'/notify.php',
			'trade_type'	=> 'JSAPI',
			'openid'		=> $openid
		);
		$unifiedorder['sign'] = self::makeSign($unifiedorder);
		
		//return $unifiedorder;
		
		//请求数据,统一下单
		$xmldata = self::array2xml($unifiedorder);
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = self::curl_post_ssl($url, $xmldata);
        if(!$res){
			return array('status'=>0, 'msg'=>"Can't connect the server" );
        }
		// 这句file_put_contents是用来查看服务器返回的结果 测试完可以删除了
		//file_put_contents('./log.txt',$res,FILE_APPEND);
		
		$content = self::xml2array($res);
		if(strval($content['result_code']) == 'FAIL'){
			return array('status'=>0, 'msg'=>strval($content['err_code']).':'.strval($content['err_code_des']));
        }
		if(strval($content['return_code']) == 'FAIL'){
			return array('status'=>0, 'msg'=>strval($content['return_msg']));
        }
        
		$time = time();
		settype($time, "string");  		//jsapi支付界面,时间戳必须为字符串格式
		$resdata = array(
            'appId'      	=> strval($content['appid']),
			'nonceStr'      => strval($content['nonce_str']),
            'package'       => 'prepay_id='.strval($content['prepay_id']),
            'signType'		=> 'MD5',
			'timeStamp'		=> $time
        );
		$resdata['paySign'] = self::makeSign($resdata);
		
		return json_encode($resdata);
	}
	
	/**
     * 微信退款(POST)
     * @param string(28) $transaction_id 	在微信支付的时候,微信服务器生成的订单流水号,在支付通知中有返回
     * @param string $out_refund_no 		商品简单描述
     * @param string $total_fee 			微信支付的时候支付的总金额(单位:分)
     * @param string $refund_fee 			此次要退款金额(单位:分)
     * @return string						xml格式的数据
     */
	public function refund($transaction_id,$out_refund_no,$total_fee,$refund_fee){
		$config = $this->config;
		//退款参数
		$refundorder = array(
			'appid'			=> $config['appid'],
			'mch_id'		=> $config['mch_id'],
			'nonce_str'		=> self::getNonceStr(),
			'transaction_id'=> $transaction_id,
			'out_refund_no'	=> $out_refund_no,
			'total_fee'		=> $total_fee * 100,
			'refund_fee'	=> $refund_fee * 100
		);
		$refundorder['sign'] = self::makeSign($refundorder);
		
		//请求数据,进行退款
		$xmldata = self::array2xml($refundorder);
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $res = self::curl_post_ssl($url, $xmldata);
        if(!$res){
			return array('status'=>0, 'msg'=>"Can't connect the server" );
        }
		// 这句file_put_contents是用来查看服务器返回的结果 测试完可以删除了
		//file_put_contents('./log3.txt',$res,FILE_APPEND);
		
		$content = self::xml2array($res);
		if(strval($content['result_code']) == 'FAIL'){
			return array('status'=>0, 'msg'=>strval($content['err_code']).':'.strval($content['err_code_des']));
        }
		if(strval($content['return_code']) == 'FAIL'){
			return array('status'=>0, 'msg'=>strval($content['return_msg']));
        }
		
		return $content;
	}
	
//-------------------------------------------------------------------------------------------------------------------------------
	
//---------------------------------------------------------------用到的函数------------------------------------------------------
	/**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    protected function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml>" : '';
        foreach($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if(!is_array($value)) {
                $s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s."</xml>" : $s;
    }
	
	/**
	 * 将xml转为array
	 * @param  string 	$xml xml字符串
	 * @return array    转换得到的数组
	 */
	protected function xml2array($xml){   
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
		return $result;
	}
	
	/**
	 * 
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	protected function getNonceStr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}
	
	/**
	* 生成签名
	* @return 签名
	*/
	protected function makeSign($data){
		//获取微信支付秘钥
		$key = $this->config['pay_apikey'];
		// 去空
		$data=array_filter($data);
		//签名步骤一：按字典序排序参数
		ksort($data);
		$string_a=http_build_query($data);
		$string_a=urldecode($string_a);
		//签名步骤二：在string后加入KEY
		$string_sign_temp=$string_a."&key=".$key;
		//签名步骤三：MD5加密
		$sign = md5($string_sign_temp);
		// 签名步骤四：所有字符转为大写
		$result=strtoupper($sign);
		return $result;
	}
	
	/**
	 * 获取IP地址
	 * @return [String] [ip地址]
	 */
	protected function getip() {
        static $ip = '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
	
	/**
	 * 微信支付发起请求
	 */
	protected function curl_post_ssl($url, $xmldata, $second=30,$aHeader=array()){
		$config = $this->config;
		
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,$config['api_cert']);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$config['api_key']);
		
		//curl_setopt($ch,CURLOPT_CAINFO,$config['rootca']);
	 
		if( count($aHeader) >= 1 ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}
	 
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xmldata);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}else { 
			$error = curl_errno($ch);
			echo "call faild, errorCode:$error\n"; 
			curl_close($ch);
			return false;
		}
	}
	
	
	//测试支付
	public function paytest(){
		$openid = 'ovprvtzBZaWXnZUadwgexOLNc93M';
		$total_fee = 0.01;
		$body = '江南极客';
		$order_sn = date('YmdHis').mt_rand(1000,9999);
		$res = self::wxpay($openid,$total_fee,$body,$order_sn);
		return $res;
	}
	
	//测试退款
	public function refundtest(){
		$transaction_id = '4200000045201712028527165897';
		$out_refund_no = date('YmdHis').mt_rand(1000,9999);
		$total_fee = 0.01;
		$refund_fee = 0.01;
		$res = self::refund($transaction_id,$out_refund_no,$total_fee,$refund_fee);
		return $res;
	}
}

/*===============================================使用方法=======================================================


//使用方法一:
	* 配置好自己的参数,注意这里的两个证书路径得根据你自己的项目证书路径来写,同时存放证书的目录要开放可读权限
	* 单纯的支付不需要证书 , 退款的时候需要证书
	$config = array(
		'appid'		=> 'wx123456789876',
		'mch_id'	 	=> '123456789',
		'pay_apikey' 	=> '123456789876123456789876123456789876',
		'api_cert'		=> getcwd().'/cert/apiclient_cert.pem',	
		'api_key'		=> getcwd().'/cert/apiclient_key.pem'
	);

	$wxpay = new WxPay($config);												//初始化类(同时传递参数)
	$data = $wxpay->wxpay($openid,$total_fee,$body,$order_sn);					//微信支付,将返回值$data(json格式)返回给页面,进行JSAPI支付
	$wxpay->refund($transaction_id,$out_refund_no,$total_fee,$refund_fee);		//微信退款
	
	
//使用方法二:
	$wxpay = new WxPay();											//初始化类
	
	$wxpay->appid 		= 'wx123456789876';							//配置参数
	$wxpay->mch_id 		= '123456789';
	$wxpay->pay_apikey 	= '123456789876123456789876123456789876';
	$wxpay->api_cert 		= getcwd().'/cert/apiclient_cert.pem';
	$wxpay->api_key 		= getcwd().'/cert/apiclient_key.pem';

	$data = $wxpay->wxpay($openid,$total_fee,$body,$order_sn);					//微信支付,将返回值$data(json格式)返回给页面,进行JSAPI支付
	$wxpay->refund($transaction_id,$out_refund_no,$total_fee,$refund_fee);		//微信退款

================================================================================================================*/