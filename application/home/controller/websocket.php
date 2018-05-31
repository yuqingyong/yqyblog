<?php

// ------------------------------------------------
//  WebSocket服务器
// ------------------------------------------------

// 创建服务器
$ws = new swoole_websocket_server('0.0.0.0', 9501);

// 监听客户端连接
$ws->on('open', function ($ws, $request) {
    $fd = $request->fd;
    setClientId($fd, microtime(true));
    $tops = getTopMsg(true);
    if (empty($tops)) return;
    $time = time();
    foreach ($tops as $k => $top) {
        unset($tops[$k]['type']);
        unset($tops[$k]['uid']);
        $tokens = dLoginId($top['unique']);
        $userInfo = get_user_info(substr($tokens[2], 1, -1));
        $from = get_level_info($userInfo['free_coin']);
        $from['username'] = $userInfo['username'];
        $from['nickname'] = $userInfo['nickname'];
        $from['header_img'] = $userInfo['header_img'];
        $tops[$k]['from'] = $from;
        $tops[$k]['d'] = count($tops[$k]['d']);
        // 综合赞、踩，计算剩余时间
        $tops[$k]['l'] = $top['b'] + 100 + $top['y'] - $top['n'] - $time;
    }
    pushOne($ws, $fd, ['i' => $tops, 'type' => 'u', 'w' => WaitLength()]);
});

// 监听客户端发送消息
$ws->on('message', function ($ws, $request) {
    $msg = json_decode($request->data, true);
    if (!is_array($msg) || !key_exists('type', $msg)) return;
    $type = $msg['type'];
    if (!in_array($type, ['d', 'n', 'y']) && (!in_array($type, ['t', 'i', 'u', 'r']) || empty($msg[$type]))) return;
    if (empty($msg['loginId'])) return;
    $Lids = dLoginId($msg['loginId']);
    if ($Lids === false) return;
    $uid = $Lids[2];
    if (!preg_match('/^[1-9]\d*$/', $uid)) return;
    $self = $request->fd;
    if ($Lids[0] > 2) return pushOne($ws, $self, ['type' => 'e', 'msg' => '网络繁忙，请稍后再试']);
    $microtime = microtime(true);
    if (microtime(true) - getClientTime($self) < 0.1) return pushOne($ws, $self, ['type' => 'e', 'msg' => '手速过快']);
    setClientId($self, $microtime);
    unset($msg['loginId']);
    // 获取用户信息
    $userInfo = get_user_info($uid);
    if (empty($userInfo)) return;
    // 判断消息类型
    if ($type === 't' || $type === 'i') {
        // 首次发言
        $first = $userInfo['first_msg'] < 1;
        if ($first) {
            $userInfo['air_coin'] += 0.5;
            $userInfo['first_msg'] = 1;
        }
        // 发言扣费
        $cost = $pay = 0.001;
        $userInfo = countCoins($userInfo, $uid, $pay, $cost);
        if ($userInfo === false) {
            // 首次发言更新
            $first && set_user_info($uid, $userInfo);
            return pushOne($ws, $self, ['type' => 'e', 'msg' => '发言扣币' . $cost . '，您币数不足']);
        }
        // 生成消息唯一token
        $msg['unique'] = eLoginId('{' . $uid . '}');
        // 发送者等级、称号
        $from = get_level_info($userInfo['free_coin']);
        $from['username'] = $userInfo['username'];
        $from['is_kefu'] = $userInfo['is_kefu'];
        $from['nickname'] = $userInfo['nickname'];
        $from['header_img'] = $userInfo['header_img'];
        $msg['from'] = $from;
        // 去除html实体
        $msg[$type] = htmlspecialchars($msg[$type]);
        // 推送消息给自己
        $msg['me'] = 1;
        pushOne($ws, $self, $msg);
        // 推送消息给其它客户端
        $msg['me'] = 0;
        pushOther($ws, $self, $msg);
    } else {
        // 验证唯一token
        if (empty($msg['unique'])) return;
        $unique = $msg['unique'];
        $tokens = dLoginId($unique);
        if (!preg_match('/^\{[1-9]\d*\}$/', $tokens[2])) return;
        if ($type === 'u') {
            // 获取当前置顶
            $tops = getTopMsg(true);
            // 判断是否有重复
            if (topExists($unique, $tops) !== false) return pushOne($ws, $self, ['type' => 'e', 'msg' => '该消息已置顶，请勿重复置顶']);
            // 置顶计费
            $cost = 5;
            $deposit = 10;
            $pay = $cost + $deposit;
            $userInfo = countCoins($userInfo, $uid, $pay, $cost);
            if ($userInfo === false) return pushOne($ws, $self, ['type' => 'e', 'msg' => '置顶需押金' . $deposit . '，扣币' . $cost . '，您币数不足']);
            // 置顶信息
            $top = $msg;
            $top['uid'] = $uid;
            unset($msg['type']);
            // 加入等候队列
            inWaitList($top);
            // 如果没有置顶则开启定时
            count($tops) > 0 || topTimer($ws);
        } elseif ($type === 'r') {
            $val = $msg['r'];
            if ($val != 1 && $val != -1) return;
            setReportResult($unique, $uid, $val);
            pushOne($ws, $self, ['type'=>'u', 'o'=>$unique]);
        } else {
            $tops = getTopMsg(true);
            $index = topExists($unique, $tops);
            if ($index === false) return;
            $pay = $cost = $type === 'd' ? 0.5 : 0.1;
            $info = ($type === 'd' ? '举报' : ($type === 'y' ? '赞' : '踩')) . '扣币' . $cost . '，您币数不足';
            $userInfo = countCoins($userInfo, $uid, $pay, $cost);
            if ($userInfo === false) return pushOne($ws, $self, ['type' => 'e', 'msg' => $info]);
            if ($type === 'd') {
                if (in_array($uid, $tops[$index]['d'])) return pushOne($ws, $self, ['type' => 'e', 'msg' => '您已举报过该信息，不能重复举报']);
                $tops[$index]['d'][] = $uid;
            } else {
                $tops[$index][$type]++;
            }
            setTopMsg($tops);
            // 推送变化
            $msg = $tops[$index];
            unset($msg['type']);
            unset($msg['uid']);
            $tokens = dLoginId($msg['unique']);
            $userInfo = get_user_info(substr($tokens[2], 1, -1));
            $from = get_level_info($userInfo['free_coin']);
            $from['username'] = $userInfo['username'];
            $from['is_kefu'] = $userInfo['is_kefu'];
            $from['nickname'] = $userInfo['nickname'];
            $from['header_img'] = $userInfo['header_img'];
            $msg['from'] = $from;
            $msg['l'] = $msg['b'] + 100 + $msg['y'] - $msg['n'] - time();
            $msg['d'] = count($msg['d']);
            pushOne($ws, $self, ['i' => [$msg], 'type' => 'u', 'w' => WaitLength()]);
        }
    }
});

// 监听客户端关闭连接
$ws->on('close', function ($ws, $fd) {
    delClientId($fd);
});

// 启动服务器
$ws->start();

// ------------------------------------------------
//  置顶定时器
// ------------------------------------------------

function topTimer($ws, $time = 1000)
{
    swoole_timer_tick($time, function ($id) use ($ws) {
        // 定时推送信息数组
        $add = [];
        $upd = [];
        $del = [];
        $reports = [];
        $time = time();
        // 获取当前置顶
        $tops = getTopMsg(true);
        $newTops = [];
        $len = 0;
        // 处理原置顶部分
        foreach ($tops as $k => $top) {
            if (count($top['d']) < 1) {
                // 综合赞、踩，计算剩余时间
                $l = $top['b'] + 100 + $top['y'] - $top['n'] - $time;
                if ($l > 0) {    // 正常流程
                    $newTops[++$len] = $top;
                    $top['l'] = $l;
                    unset($top['type']);
                    unset($top['uid']);
                    unset($top['b']);
                    unset($top['u']);
                    unset($top['from']);
                    $top['d'] = count($top['d']);
                    $upd[] = $top;
                } else {    // 置顶到期
                    // 返回置顶押金
                    newAirCoin($top['uid'], 10);
                    // 结束
                    $del[] = $top['unique'];
                }
            } else {    // 达到举报审核条件
                // 加入举报列表
                $report = ['u' => $top['u'], 'unique' => $top['unique'], 'uid' => $top['uid']];
                addReport($report);
                // 开启定时
                reportTimer($ws, $report);
                // 结束
                $del[] = $top['unique'];
                // 举报推送
                $reports[] = ['u' => $top['u'], 'unique' => $top['unique']];
            }
        }
        // 补充置顶[上限3个]
        while ($len < 3 && $top = outWaitList(true)) {
            $len++;
            $top['b'] = $time;   // 开启时间
            $top['y'] = 0;       // 赞数
            $top['n'] = 0;       // 踩数
            $top['d'] = [];      // 举报数组
            $newTops[] = $top;
            $top['l'] = 100;     // 剩余时间
            unset($top['type']);
            unset($top['uid']);
            unset($top['b']);
            $tokens = dLoginId($top['unique']);
            $userInfo = get_user_info(substr($tokens[2], 1, -1));
            $from = get_level_info($userInfo['free_coin']);
            $from['username'] = $userInfo['username'];
            $from['nickname'] = $userInfo['nickname'];
            $from['header_img'] = $userInfo['header_img'];
            $top['from'] = $from;
            $top['d'] = 0;
            $add[] = $top;
        }
        // 更新置顶信息
        setTopMsg($newTops);
        // 推送置顶信息到客户端
        pushAll($ws, ['type' => 'u', 'i' => $add, 'u' => $upd, 'd' => $del, 'w' => WaitLength(), 'r'=>$reports]);
        // 如无置顶清除定时器
        if ($len === 0) swoole_timer_clear($id);
    });
}

// ------------------------------------------------
//  举报定时器
// ------------------------------------------------

function reportTimer($ws, $report)
{
    swoole_timer_after(1800000, function () use ($ws, $report) {
        // 结算举报评判
        $res = 0;
        $unique = $report['unique'];
        foreach (getReportResults($unique) as $v) {
            $res += $v;
        }
        if ($res <= 0) {
            $uid = $report['uid'];
            $userInfo = get_user_info($uid);
            $userInfo['air_coin'] += 12.5;
            set_user_info($uid, $userInfo);
        }
        // 删除举报消息
        delReport($unique);
        // 删除举报结果
        delReportResults($unique);
        // 提醒客户端评判结束
        pushAll($ws, ['type' => 'u', 'o' => $unique]);
    });
}

// ------------------------------------------------
//  举报结果哈希
// ------------------------------------------------

// 取全部值
function getReportResults($unique)
{
    return RedisObj()->hVals('rep_' . $unique);
}

// 设置
function setReportResult($unique, $uid, $val)
{
    return RedisObj()->hSet('rep_' . $unique, $uid, $val);
}

// 删除
function delReportResults($unique)
{
    return RedisObj()->del('rep_' . $unique);
}

// 判断用户是否已评判
function existsReportResult($unique, $uid)
{
    return RedisObj()->hExists('rep_' . $unique, $uid);
}

// ------------------------------------------------
//  扣费处理
// ------------------------------------------------

function countCoins($userInfo, $uid, $pay, $cost)
{
    // 判断余额
    if ($userInfo['air_coin'] < $pay) return false;
    // 计费
    $userInfo['air_coin'] -= $pay;
    $userInfo['free_coin'] += $cost;
    // 更新用户信息
    set_user_info($uid, $userInfo);
    return $userInfo;
}

// ------------------------------------------------
//  更新余额
// ------------------------------------------------

function newAirCoin($uid, $air_coin)
{
    $userInfo = get_user_info($uid);
    $userInfo['air_coin'] += $air_coin;
    set_user_info($uid, $userInfo);
}

// ------------------------------------------------
//  审核列表
// ------------------------------------------------

// 添加
function addReport($report)
{
    return RedisObj()->hSet('report_msg', $report['unique'], json_encode($report));
}

// 删除指定举报消息
function delReport($unique)
{
    return RedisObj()->hDel('report_msg', $unique);
}

// 获取全部值
function getReports() {
    return RedisObj()->hVals('report_msg');
}

// ------------------------------------------------
//  置顶相关
// ------------------------------------------------

// 获取置顶列表
function getTopMsg($isArr = false)
{
    return json_decode(RedisObj()->get('top_msg'), $isArr) ?: [];
}

// 设置置顶列表
function setTopMsg($tops)
{
    return RedisObj()->set('top_msg', json_encode($tops));
}

// 判断重复
function topExists($unique, $tops = null)
{
    is_null($tops) && $tops = getTopMsg(true);
    foreach ($tops as $k => $v) {
        if ($v['unique'] === $unique) return $k;
    }
    return false;
}

// ------------------------------------------------
//  置顶等候队列
// ------------------------------------------------

// 进
function inWaitList($top)
{
    return RedisObj()->rPush('wait_msg', json_encode($top));
}

// 出
function outWaitList($isArr = false)
{
    return json_decode(RedisObj()->lPop('wait_msg'), $isArr);
}

// 长度
function WaitLength()
{
    return RedisObj()->lLen('wait_msg');
}

// ------------------------------------------------
//  等级、称号
// ------------------------------------------------

// 计算等级、称号
function get_level_info($free_coin)
{
    $level_list = get_level_list();
    $res = false;
    foreach ($level_list as $v) {
        if ($v['min_coin'] <= $free_coin) {
            $res = ['level'=>$v['level']];
        }
    }
    return $res;
}

// 获取等级列表
function get_level_list()
{
    $redis = RedisObj();
    $level_list = json_decode($redis->get('level_list'), true);
    if (empty($level_list)) {
        $level_list = pdoSql(" SELECT `level`,min_coin FROM yqy_user_level ");
        $redis->set('level_list', json_encode($level_list));
    }
    return $level_list;
}

// ------------------------------------------------
//  周期消费用户
// ------------------------------------------------

// 设置
function setActiveUids($uid)
{
    return RedisObj()->sAdd('life_msg_uid', $uid);
}

// 判断是否存在
function uidExists($uid)
{
    return RedisObj()->sIsMember('life_msg_uid', $uid);
}

// ------------------------------------------------
//  用户信息存取
// ------------------------------------------------

function set_user_info($uid, $data)
{
    uidExists($uid) || setActiveUids($uid);
    return RedisObj()->set('userinfo_' . $uid, json_encode($data));
}

function get_user_info($uid)
{
    $redis = RedisObj();
    $key = 'userinfo_' . $uid;
    if ($redis->exists($key)) {
        $info = json_decode($redis->get($key), true);
    } else {
        $info = pdoSql(" SELECT username,free_coin,air_coin,header_img,nickname,first_msg,is_kefu,coin_time FROM yqy_member WHERE uid = $uid; ");
        if (empty($info)) return false;
        $info = $info[0];
        $redis->set($key, json_encode($info));
    }
    return $info;
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
    foreach (getClientIds() as $fd) {
        pushOne($ws, $fd, $msg, $str);
    }
}

function pushOther($ws, $fd, $msg, $str = false)
{
    foreach (getClientIds() as $v) {
        $fd == $v || pushOne($ws, $v, $msg, $str);
    }
}

// ------------------------------------------------
//  loginId
// ------------------------------------------------

// 加密
function encode($str)
{
    if (mb_strlen($str) <= 1) return '';
    // 密锁串、长度、随机位及值
    $lock = 'tRL+CS1A967JBGMVem4Und5xac-_bzF0YNpWojk2vgIs8ThZ3=/EXOuHKrwPfiQDqly';
    $len = strlen($lock);
    $rand = mt_rand(0, $len - 1);
    $lk = $lock[$rand];
    // 密钥结合密锁随机值MD5加密
    $md5 = strtoupper(md5('0290ad5ed58b92959e2d567bf2648c5c' . $lk));
    // 字符串BASE64加密
    $str = base64_encode($str);
    $res = '';
    for ($i = $k = 0, $c = strlen($str); $i < $c; $i++) {
        $k === strlen($md5) && $k = 0;
        // 转化字符：由密锁串 原位+随机位+顺序MD5密钥字符ASCII码 决定新位，从密锁串中获取目标字符
        $res .= $lock[(strpos($lock, $str[$i]) + $rand + ord($md5[$k])) % ($len)];
        $k++;
    }
    // 返回加密结果(含随机关联)
    return $res . $lk;
}

// 解密
function decode($str)
{
    if (mb_strlen($str) <= 1) return '';
    // 将地址栏参数被强制转换的空格替换成+号
    $str = str_replace(' ', '+', $str);
    // 密锁串、长度、随机位及值
    $lock = 'tRL+CS1A967JBGMVem4Und5xac-_bzF0YNpWojk2vgIs8ThZ3=/EXOuHKrwPfiQDqly';
    $len = strlen($lock);
    // 字符串长度
    $txtLen = strlen($str);
    // 密锁随机值及位
    $lk = $str[$txtLen - 1];
    $rand = strpos($lock, $lk);
    // 密钥结合密锁随机值MD5加密
    $md5 = strtoupper(md5('0290ad5ed58b92959e2d567bf2648c5c' . $lk));
    // 去除字符串随机关联
    $str = substr($str, 0, $txtLen - 1);
    $tmpStream = '';
    for ($i = $k = 0, $c = strlen($str); $i < $c; $i++) {
        $k === strlen($md5) && $k = 0;
        // 获取字符在密锁串原位：由 位-随机位-顺序MD5密钥字符ASCII码 算出
        $j = strpos($lock, $str[$i]) - $rand - ord($md5[$k]);
        while ($j < 0) {
            $j += $len;
        }
        $tmpStream .= $lock[$j];
        $k++;
    }
    // 返回BASE64解密源字符串
    return base64_decode($tmpStream);
}

// 生成登录ID
function eLoginId($str)
{
    return encode(time() . '.' . chr(mt_rand(97, 122)) . '.' . $str);
}

// 登录ID解码
function dLoginId($loginId)
{
    $arr = explode('.', decode($loginId));
    if (count($arr) !== 3) return false;
    $t = $arr[0];
    if (!is_numeric($t)) return false;
    $pass = time() - $t;
    if ($pass < 0) return false;
    $arr[0] = $pass;
    $latin = $arr[1];
    if (strlen($latin) !== 1) return false;
    $ord = ord($latin);
    if ($ord > 122 || $ord < 97) return false;
    return $arr;
}

// ------------------------------------------------
//  客户端ID：设置、删、获取时间、获取全部ID
// ------------------------------------------------

function setClientId($fd, $time)
{
    return RedisObj()->hSet('prison_team', $fd, $time);
}

function delClientId($fd)
{
    return RedisObj()->hDel('prison_team', $fd);
}

function getClientTime($fd)
{
    return RedisObj()->hGet('prison_team', $fd);
}

function getClientIds()
{
    return RedisObj()->hKeys('prison_team');
}

// ------------------------------------------------
//  pdo语句
// ------------------------------------------------

function pdoSql($sql, $bind = [], $exec = false)
{
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=oursql", 'xml', 'MM:xml310');
        $pdo->query('SET NAMES utf8');
    } catch (\PDOException $e) {
        file_put_contents('sql_log', date('Y-m-d H:i:s') . '=' . iconv("GB2312//IGNORE", "UTF-8", $e->getMessage()), FIEL_APPEND);
    }
    $bind = empty($bind) ? false : $bind;
    if ($bind) {
        $sth = $pdo->prepare($sql);
        $sth->execute($bind);
    } else {
        $sth = $exec ? $pdo->exec($sql) : $pdo->query($sql);
    }
    if ($exec) {
        $pdo = null;
        return $sth;
    } else {
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $arr = [];
        while ($v = $sth->fetch()) {
            $arr[] = $v;
        }
        $sth = null;
        return $arr;
    }
}

// ------------------------------------------------
//  Redis对象
// ------------------------------------------------

function RedisObj()
{
    static $redis = null;
    if (is_null($redis)) {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('csntsyx');
    }
    return $redis;
}
