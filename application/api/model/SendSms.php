<?php 
namespace app\api\model;
require_once "../extend/Alidayu/SignatureHelper.php";
/**
* 阿里大于短信类
*/
class SendSms
{
	/**
     * 发送短信
     * @return stdClass
     */
    function sendSms($accessKeyId,$accessKeySecret,$phone,$SignName,$TemplateCode) {
	    $params = array ();

	    // *** 需用户填写部分 ***

	    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
	    $accessKeyId = $accessKeyId;
	    $accessKeySecret = $accessKeySecret;

	    // fixme 必填: 短信接收号码
	    $params["PhoneNumbers"] = $phone;

	    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
	    $params["SignName"] = $SignName;

	    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
	    $params["TemplateCode"] = $TemplateCode;

	    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
	    $params['TemplateParam'] = Array (
	        "code" => rand(1000,9999),
	        "product" => "阿里通信"
	    );

	    // fixme 可选: 设置发送短信流水号
	    // $params['OutId'] = "12345";

	    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
	    // $params['SmsUpExtendCode'] = "1234567";

	    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
	    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
	        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
	    }

	    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
	    $helper = new \SignatureHelper();

	    // 此处可能会抛出异常，注意catch
	    $content = $helper->request(
	        $accessKeyId,
	        $accessKeySecret,
	        "dysmsapi.aliyuncs.com",
	        array_merge($params, array(
	            "RegionId" => "cn-hangzhou",
	            "Action" => "SendSms",
	            "Version" => "2017-05-25",
	        ))
	        // fixme 选填: 启用https
	        // ,true
	    );

	 	// object(stdClass)#18 (4) {
		//   ["Message"] => string(2) "OK"
		//   ["RequestId"] => string(36) "DFB5DB44-4E5F-4204-BF19-6CFE344E7A14"
		//   ["BizId"] => string(20) "232007230596275735^0"
		//   ["Code"] => string(2) "OK"
		// }
	    return $content;
	}
}