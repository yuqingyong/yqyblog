<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//获取客户端IP地址
function get_real_ip()
{
	$ip=false;
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
	  $ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	  $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
	  if($ip){
	   array_unshift($ips, $ip); $ip = FALSE;
	  }
	  for($i = 0; $i < count($ips); $i++){
	   if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])){
	    $ip = $ips[$i];
	    break;
	   }
	  }
	}
	return($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}



function getpic($content){
	$data['content']=$content;//获取的内容  
	$soContent=$data['content'];  
	$soImages = '~<img [^>]* />~';  
	preg_match_all($soImages, $soContent, $thePics);
	$allPics = count($thePics[0]);  
	preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|PNG))\"?.+>/i',$thePics[0][0],$match);  
	$data['ig']=$thePics[0][0];  
	//dump($data['img']);  
	if($allPics> 0){  
		return $match[1];  
	}else {  
		return null;  
	}  
}
  
//功能：计算两个时间戳之间相差的日时分秒
//$begin_time  开始时间戳
//$end_time 结束时间戳
function timediff($begin_time,$end_time)
{
      if($begin_time < $end_time){
         $starttime = $begin_time;
         $endtime = $end_time;
      }else{
         $starttime = $end_time;
         $endtime = $begin_time;
      }

      //计算天数
      $timediff = $endtime-$starttime;
      $days = intval($timediff/86400);
      //计算小时数
      $remain = $timediff%86400;
      $hours = intval($remain/3600);
      //计算分钟数
      $remain = $remain%3600;
      $mins = intval($remain/60);
      //计算秒数
      $secs = $remain%60;
      $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
      return $res['day'];
}



/**
 * 三级分类查询
 * @return array 查询的结果
 */
function third_category($list,$fid)
{
	$arr = array();
    foreach ($list as $key => $value) {
        if ($value['pid'] == $fid) {
            $value['child'] = third_category($list,$value['cmtid']);
            $arr[] = $value;
        }
        
    }
    return $arr;
}

/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}




