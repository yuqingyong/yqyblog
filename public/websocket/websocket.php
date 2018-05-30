<?php
//创建websocket服务器
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//创建临时存储客户端ID文件
$client_id = date('Ymd').'.json';

//监听客户端ID
$ws->on('open', function($ws, $req) {
    //存储客户端ID
	global $client_id;
	$team = getName();
	$team[$req->fd] = $req->fd;
	//使用文件的方式存储
	file_put_contents($client_id, json_encode($team));
});

//监听客户端发送消息
$ws->on('message', function($ws, $req) {
    $msg = json_decode($req->data,true);
    $self = $req->fd;
    $type = $msg['type'];
	 // 推送消息给自己
    $msg['me'] = 1;
    pushOne($ws, $self, $msg);
    // 推送消息给其它客户端
    $msg['me'] = 0;
    pushOther($ws, $self, $msg);
});

//关闭客户端ID
$ws->on('close', function($ws, $fd) {
    //用户关闭了客户端，则重新更新用户ID
    global $client_id;
    $team = getName();
    unset($team[$fd]);
    file_put_contents($client_id, json_encode($team));
});

//开启
$ws->start();

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
    return $ws->push($fd, $msg);
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
}