//定义自己的聊天消息模板
var _else_tpl = '<table class="msg-tb msg-other"><tbody><tr><td class="msg-head" rowspan="2"><img src="header.jpg" alt=""></td><td class="msg-title">{username}</td></tr><tr><td class="msg-text"><span onclick="show_big_pic()" class="msg-span"><div class="t_div">{content}</div></span></td></tr></tbody></table>';

//定义他人发送消息的模板
var _my_tpl = '<table class="msg-tb msg-me"><tbody><tr><td class="msg-title">{username}</td><td rowspan="2" class="msg-head"><img src="header.jpg" alt=""></td></tr><tr><td class="msg-text"><span onclick="show_big_pic()" class="msg-span"><div class="t_div">{content}</div></span></td></tr></tr></tbody></table>';

// 消息对象
(window.ws = new WebSocket("ws://139.196.93.247:9502")).onmessage = function (e) {
    showMsg(e.data);
};

//输入框对象
var user_input = document.getElementById('input_msg');
// 输入框默认焦点
user_input.focus();
//图片
var _user_img = document.getElementById('user_img');

function send_pic(){
	_user_img.click();
}

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
	var type = data.type;
	console.log(type)
	var str = '';
	var tp = data.me ? _my_tpl : _else_tpl;
	// str = tp.replace('{username}','yqy').replace('{content}',analysis_emoji(data.t));
	str = (type === 't') ? tp.replace('{username}','yqy').replace('{content}',analysis_emoji(data.t)) : tp.replace('{username}','yqy').replace('{content}', '<img class="t_img" title="点击查看大图" src="' + data.i + '"/>');

	$("#content").append(str);
}

//显示大图
function show_big_pic(){
	$(".big_img").find('img').attr('src', $(this).find('.t_img').attr('src'));
    $(".big_img").show();
}


//发送消息
function sendMsg() {
	var content = $("#input_msg").val();
	ws.send('{"type":"t", "t":"'+content+'"}');
	user_input.value = '';
    user_input.focus();
    $("#content").scrollTop( $("#content")[0].scrollHeight);
}


// 显示表情
function show_biaoqing() {
    var row = 5, col = 15;
    var str = '<table class="emoji">';
    for (var i = 0; i < row; i++) {
        str += '<tr>';
        for (var j = 0; j < col; j++) {
            var n = i * col + j;
            str += '<td>' + (n > 71 ? '' : ('<img onclick="select_emoji(' + n + ');" src="face/' + n + '.gif" />')) + '</td>';
        }
        str += '</tr>';
    }
    str += '</table>';
    $('.emoji_div').html(str);
}

// 选择表情
function select_emoji(n) {
    cursor_insert(user_input, '{{@' + n + '}}');
    $(".emoji_div").fadeOut();
}

// 光标处插入内容
function cursor_insert(obj, txt) {
    if (document.selection) {
        obj.selection.createRange().text = txt;
    } else {
        var v = obj.value;
        var i = obj.selectionStart;
        obj.value = v.substr(0, i) + txt + v.substr(i);
        user_input.focus();
        obj.selectionStart = i + txt.length;
    }
}

// 解析消息中的表情
function analysis_emoji(str) {
    var p = /{{@(\d|[1-6]\d|7[01])}}/;
    if (p.test(str)) {
        return analysis_emoji(str.replace(p, "<img src='face/$1.gif'/>"))
    } else {
        return str;
    }
}

// 上传图片
function load_img(f){
	_7ui.upload('/home/Test/send_chat_img', {file_img: f}, function (e) {
        var json = JSON.parse(e);
        if (json.status == 0) {
            _7ui.info(json.msg);
        } else {
            ws.send('{"type":"i", "i":"' + json.img + '"}');
            user_input.value = '';
            user_input.focus();
        }
        $("#user_img").val('');
    });
}
