<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Chat\add_chat.html";i:1516915804;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="renderer" content="webkit">
<title></title>
<link rel="stylesheet" href="__PUBLIC__/admin/css/pintuer.css">
<link rel="stylesheet" href="__PUBLIC__/admin/css/admin.css">
<script src="__PUBLIC__/admin/js/jquery.js"></script>
<script src="__PUBLIC__/admin/js/pintuer.js"></script>
<script src="__PUBLIC__/admin/js/layer.js"></script>
</head>
<body>
<div class="panel admin-panel">
  <div class="panel-head" id="add"><strong><span class="icon-pencil-square-o"></span>添加随笔</strong></div>
  <div class="body-content">
    <form method="post" class="form-x" action="<?php echo url('admin/Chat/add_chat'); ?>">  
      <div class="form-group">
        <div class="label">
          <label>内容：</label>
        </div>
        <div class="field">
          <textarea type="text" class="input w50" name="content" style="width: 400px;height: 100px" /></textarea>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label></label>
        </div>
        <div class="field">
          <button class="button bg-main icon-check-square-o" type="submit"> 提交</button>
        </div>
      </div>
    </form>
  </div>
</div>

</body>
</html>