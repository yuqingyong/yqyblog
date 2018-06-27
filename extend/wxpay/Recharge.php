<?php
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
        $this->params = [
            'mch_appid'         => self::APPID,//APPid,
            'mchid'             => self::MCHID,//商户号,
            'nonce_str'         => md5(time()), //随机字符串
            'partner_trade_no'  => $data['order_number'], //商户订单号
            'openid'            => $data['openid'], //用户openid
            'check_name'        => 'NO_CHECK',//校验用户姓名选项 NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
            //'re_user_name'    => '',//收款用户姓名  如果check_name设置为FORCE_CHECK，则必填用户真实姓名
            'amount'            => $data['price'],//金额 单位分
            'desc'              => '测试付款',//付款描述
            'spbill_create_ip'  => $_SERVER['SERVER_ADDR'],//调用接口机器的ip地址
        ];
        //将数据发送到接口地址
        return $this->send(self::PAYURL);
    }
    //签名
    public function sign(){
        return $this->setSign($this->params);
    }
    //发送请求
    public function send($url){
        $res = $this->sign();
        $xml = $this->ArrToXml($res);
       $returnData = $this->postData($url, $xml);
       return $this->XmlToArr($returnData);
    }
}
