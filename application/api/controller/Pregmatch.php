<?php 
namespace app\api\controller;
use think\Controller;
/**
* 正则获取数据并处理
*/
class Pregmatch extends Controller
{
	public function preUrlContent()
	{
		# 获取币种
		$coin = $this->request->param('coin');
		# 请求地址
		$url = "https://gateio.io/trade/".$coin;
		# 获取文本
		$html = file_get_contents($url);
		# 匹配需要的字符串
		$rule = '/<ul id="ul\-ask\-list" data\-id= "ask\-list">(?<sell>.*?)<\/ul>.*?<ul id="ul\-bid\-list" data\-id= "bid\-list">(?<buy>.*?)<\/ul>/s';
		# 匹配开始
		preg_match($rule,$html,$m);
		# 分别取得卖出sell，和买入buy的字符串
		$sell = $m['sell'];
		$buy = $m['buy'];
		# 获取卖出的list规则，将需要的数据用（）单独获取到
		$sell_rule = '/<li  onclick=\'.*?\' class=\'.*?\'>\s*<span data\-id= "price"  class= "price right\-align" >(.*?)<\/span>\s*<span data\-id= "volume" class= "volume right\-align"  >(.*?)<\/span>\s*<span data\-id= "total" class= "right\-align total" >(.*?)<\/span>\s*<span data\-id= "rect" class= "right\-align rect down orange" style="width: .*?;"><\/span>\s*<\/li>/';
		# 获取买入的list规则，将需要的数据用（）单独获取到
		$buy_rule = '/<li  onclick=\'.*?\' class=\'.*?\'>\s*<span data\-id= "price"  class= "price right\-align" >(.*?)<\/span>\s*<span data\-id= ".*?" class= ".*?"  >(.*?)<\/span>\s*<span data\-id= "total" class= "right\-align total" >(.*?)<\/span>\s*<span data\-id= "rect" class= ".*?" style="width: .*?"><\/span>/';
		# 匹配卖出的数据
		preg_match_all($sell_rule, $sell, $sell_list);
		# 匹配买入的数据
		preg_match_all($buy_rule, $buy, $buy_list);

		# 计算数据条数
		$sell_count = count($sell_list[0]);
		# 定义一个空数组接收新的数组
		$sell_arr = [];
		if($sell_count > 0){
			for ($i=0; $i < $sell_count; $i++) { 
				# 将获取到的每个字段重新组合成一个新的数组
				$sell_arr[$i]['price'] = $sell_list[1][$i];
				$sell_arr[$i]['num'] = $sell_list[2][$i];
				$sell_arr[$i]['usdt'] = $sell_list[3][$i];
			}
		}

		$buy_count = count($buy_list[0]);
		$buy_arr = [];
		if($buy_count > 0){
			for ($i=0; $i < $buy_count; $i++) { 
				$buy_arr[$i]['price'] = $buy_list[1][$i];
				$buy_arr[$i]['num'] = $buy_list[2][$i];
				$buy_arr[$i]['usdt'] = $buy_list[3][$i];
			}
		}

		# 计算卖出的数量
		$sell_num = 0.00;
		foreach ($sell_list[2] as $k => $v) {
			$sell_num += (float)$v;
		}

		# 计算买入的数量
		$buy_num = 0;
		foreach ($buy_list[2] as $k => $v) {
			$buy_num += (float)$v;
		}

		dump($sell_num);
		dump($buy_num);
		echo "<---------------------------------------------------出售数据---------------------------------------------------------><br>";
		dump($sell_arr);
		echo "<---------------------------------------------------买入数据---------------------------------------------------------><br>";
		dump($buy_arr);die;
	}
}