<?php 
namespace think;
use think\request;
use think\Controller;
use think\Cookie;
header("Content-type: text/html; charset=utf-8");   
 
class Wechat{

	//设置公众号的appid和appsecret
	private $appid = 'wxdd811d01f8afdbab';
    private $appsecret = '684df99dcd22963c9f54825a6a1948ef';

    public function _userInfoAuth($redirect_url){  
    
	    //1.准备scope为snsapi_userInfo网页授权页面  
	    $redirecturl = urlencode($redirect_url);  
	    $snsapi_userInfo_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$redirecturl.'&response_type=code&scope=snsapi_userinfo&state=YQJ#wechat_redirect';

	    //2.用户手动同意授权,同意之后,获取code  
	    //页面跳转至redirect_uri/?code=CODE&state=STATE  
	    //$code = $_GET['code']; 
	    $code = input('get.code'); 
	    if( !isset($code) ){
	        header("Location:{$snsapi_userInfo_url}");
	    	exit;
	    }

	    //3.通过code换取网页授权access_token  
	    $curl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code';  
	    $content = $this->_request($curl);  
	    $result = json_decode($content);
	    $res = $this->object2array($result);

	    //4.通过access_token和openid拉取用户信息  
	    $webAccess_token = $res['access_token'];  
	    $openid = $res['openid'];
	    Cookie::set('openid',$openid,86400*7);
	    $userInfourl = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$webAccess_token.'&openid='.$openid.'&lang=zh_CN ';  
	    
	    $recontent = $this->_request($userInfourl);  
	    $userInfo = json_decode($recontent,true);
	    return $userInfo;  
	}


	function object2array($object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }

    //private $appid = 'wx9c254fc88353de60';
    //private $appsecret = '684df99dcd22963c9f54825a6a1948ef';

	//设置网络请求配置  
	public function _request($curl,$https=true,$method='GET',$data=null){  
	    // 创建一个新cURL资源  
	    $ch = curl_init();  
	      
	    // 设置URL和相应的选项
	    curl_setopt($ch, CURLOPT_URL, $curl);    //要访问的网站  
	    curl_setopt($ch, CURLOPT_HEADER, false);    //启用时会将头文件的信息作为数据流输出。  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将curl_exec()获取的信息以字符串返回，而不是直接输出。   
	  
	    if($https){  
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //FALSE 禁止 cURL 验证对等证书（peer's certificate）。  
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //验证主机  
	    }  
	    if($method == 'POST'){  
	        curl_setopt($ch, CURLOPT_POST, true);  //发送 POST 请求  
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //全部数据使用HTTP协议中的 "POST" 操作来发送。  
	    }  
	      
	      
	    // 抓取URL并把它传递给浏览器  
	    $content = curl_exec($ch);  
	    if ($content  === false) {  
	      return "网络请求出错: " . curl_error($ch);  
	      exit();  
	    }  
	    //关闭cURL资源，并且释放系统资源  
	    curl_close($ch);  
	      
	    return $content;  
	}  
	  
	  
	/** 
	 * 获取用户的openid 
	 * @param  string $openid [description] 
	 * @return [type]         [description] 
	 */  
	public function baseAuth($redirect_url,$state){  
	      
	    //1.准备scope为snsapi_base网页授权页面  
	    $baseurl = urlencode($redirect_url);  
	    $snsapi_base_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$baseurl.'&response_type=code&scope=snsapi_base&&state='.$state.'#wechat_redirect';
	    //2.静默授权,获取code
	    //页面跳转至redirect_uri/?code=CODE&state=STATE
	    //$code = $_GET['code'];
	    $code = input('get.code');
	    if( !isset($code) ){  
	        header('Location:'.$snsapi_base_url);  
	    }
	    dump($code);
	    //3.通过code换取网页授权access_token和openid  
	    $curl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code';
	    $content = $this->_request($curl);  
	    $result = json_decode($content,true);
	    dump($result);
	    return $result;  
	}  

	//获取令牌  
	public function getAccessToken(){  
	      
	    //指定保存文件位置  
	    if(!is_dir('./access_token/')){  
	        mkdir(iconv("GBK","UTF-8",'./access_token/'),0777,true);   
	    }  
	    $file = './access_token/token';  
	    if(file_exists($file)){  
	        $content = file_get_contents($file);  
	        $cont = json_decode($content);  
	        if( (time()-filemtime($file)) < $cont->expires_in){   //当前时间-文件创建时间<token过期时间  
	            return $cont->access_token;  
	        }  
	    }  
	      
	    $curl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;  
	    $content = $this->_request($curl);  
	    file_put_contents($file,$content);  
	    $cont = json_decode($content);  
	    return $cont->access_token;
	  
	}  
	  
	/** 
	 * 通过openid拉取用户信息 
	 * @param  string $openid [description] 
	 * @return [type]         [description] 
	 */  
	public function getUserInfo($openid=''){  
	    if(!$openid) return false;  
	    $access_token = $this->getAccessToken();  
	    $urlStr = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
	    $url = sprintf($urlStr,$access_token,$openid);  
	    $result = json_decode($this->_request($url),true);  
	    return $result;  
	}  






}


