<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Article\edit_article.html";i:1516915804;}*/ ?>
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
<script charset="utf-8" src="__PUBLIC__/admin/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="__PUBLIC__/admin/kindeditor/lang/zh-CN.js"></script>
<script>
    KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id');
    });
    var options = {
            cssPath : '/css/index.css',
            filterMode : true
    };
    var editor = K.create('textarea[name="content"]', options);

    html = editor.html();

    // 同步数据后可以直接取得textarea的value
    editor.sync();
    html = document.getElementById('editor_id').value; // 原生API
    html = K('#editor_id').val(); // KindEditor Node API
    html = $('#editor_id').val(); // jQuery
    // 设置HTML内容
    editor.html('HTML内容');
    // 关闭过滤模式，保留所有标签
    // KindEditor.options.filterMode = false;

    // KindEditor.ready(function(K)) {
    //         K.create('#editor_id');
    // }
</script>
</head>
<body>
<div class="panel admin-panel">
  <div class="panel-head" id="add"><strong><span class="icon-pencil-square-o"></span>修改文章</strong></div>
  <div class="body-content">
    <form method="post" class="form-x" action="<?php echo url('admin/Article/edit_article'); ?>" enctype="multipart/form-data">  
      <div class="form-group">
        <div class="label">
          <label>标题：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" value="<?php echo $info['title']; ?>" name="title"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>文章分类：</label>
        </div>
        <div class="field">
        <select name="cid">
			  <option value ="<?php echo $info['cid']; ?>"><?php echo $info['cname']; ?></option>
			  <?php if(is_array($category) || $category instanceof \think\Collection || $category instanceof \think\Paginator): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
			  	<option value ="<?php echo $c['cid']; ?>"><?php echo $c['cname']; ?></option>
			  <?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>作者：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" value="<?php echo $info['author']; ?>" name="author"/>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>关键词：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" value="<?php echo $info['keywords']; ?>" name="keywords"/>
        </div>
      </div>
      <div class="clear"></div>
      <div class="form-group">
        <div class="label">
          <label>排序：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" name="sort" value="<?php echo $info['sort']; ?>" />
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>点击量：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" name="click" value="<?php echo $info['click']; ?>" />
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>简单描述：</label>
        </div>
        <div class="field">
          <textarea type="text" class="input w50" name="description" /><?php echo $info['description']; ?></textarea>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label>详情：</label>
        </div>
        <div class="field">
          <textarea id="editor_id" name="content" style="width:800px;height:500px;"><?php echo $info['content']; ?></textarea>
        </div>
      </div>
      <div class="form-group">
        <div class="label">
          <label></label>
        </div>
        <div class="field">
          <input type="hidden" name="aid" value="<?php echo $info['aid']; ?>">
          <button class="button bg-main icon-check-square-o" type="submit"> 提交</button>
        </div>
      </div>
    </form>
  </div>
</div>

</body>
</html>