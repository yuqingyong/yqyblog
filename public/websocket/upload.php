<?php 
//用户上传聊天图片
function send_chat_img(){
    if (empty($_FILES['file_img'])) return;
    $arr = ['status'=>0];
    $file_img = $_FILES['file_img'];
    $type = $file_img['type'];
    if(strpos($type, 'image/') !== 0) {
        $arr['msg'] = '请上传jpg/png/gif格式的图片';
        return json_encode($arr);
    }

    if ($file_img['size'] > 1024*1024) {
        $arr['msg'] = '请上传小于1M的图片';
        return json_encode($arr);
    }

    $header = 'data:'.$type.';base64,';
    $content = base64_encode(file_get_contents($file_img['tmp_name']));
    $arr['status'] = 1;
    $arr['img'] = $header.$content;
    return json_encode($arr);
}




 ?>