<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Config\edit_config.html";i:1530581101;}*/ ?>
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
  <div class="panel-head" id="add"><strong><span class="icon-pencil-square-o"></span>添加配置</strong></div>
  <div class="body-content">
    <form class="form-x" action="<?php echo url('Config/edit_config'); ?>" method="post">
      <div class="form-group">
        <div class="label">
          <label>配置名：</label>
        </div>
        <div class="field">
          <input type="text" class="input w50" value="<?php echo $config['config_name']; ?>" name="config_name" id="config_name" />
        </div>
      </div>
      <!-- <div class="form-group">
        <div class="label">
          <label>配置类型：</label>
        </div>
        <div class="field">
        <select name="type" id="type">
          <option value ="1">支付宝</option>
          <option value ="2">微信</option>
          <option value ="3">短信</option>
        </select>
        </div>
      </div>
 -->
      <!-- 支付宝配置参数 -->
      <?php if($config['type'] == 1): ?>
        <div class="form-group">
          <div class="label">
            <label>Appid：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appid']; ?>" name="appid" id="zfb_appid" />
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>Appsecret：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appsecret']; ?>" name="appsecret" id="zfb_appsecret" />
          </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
          <div class="label">
            <label>支付宝商户ID：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" name="mchid" value="<?php echo $config['config']['mchid']; ?>" id="zfb_mchid"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>支付宝商户密钥：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" name="mchkey" value="<?php echo $config['config']['mchkey']; ?>" id="zfb_mchkey" />
          </div>
        </div>
        <?php elseif($config['type'] == 2): ?>

      <!-- 微信配置参数 -->
        <div class="form-group">
          <div class="label">
            <label>Appid：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appid']; ?>" name="appid" id="wx_appid"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>Appsecret：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appsecret']; ?>" name="appsecret" id="wx_appsecret"/>
          </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
          <div class="label">
            <label>微信商户ID：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" name="mchid" value="<?php echo $config['config']['mchid']; ?>" id="wx_mchid"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>微信商户密钥：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" name="mchkey" value="<?php echo $config['config']['mchkey']; ?>" id="wx_mchkey"/>
          </div>
        </div>
        <?php elseif($config['type'] == 3): ?>
      <!-- 短信配置参数 -->
        <div class="form-group">
          <div class="label">
            <label>Appid：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appid']; ?>" name="appid" id="sms_appid"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>Appsecret：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['appsecret']; ?>" name="appsecret" id="sms_appsecret"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>模板ID：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['smsid']; ?>" name="smsid" id="sms_smsid"/>
          </div>
        </div>
        <div class="form-group">
          <div class="label">
            <label>短信签名：</label>
          </div>
          <div class="field">
            <input type="text" class="input w50" value="<?php echo $config['config']['sign']; ?>" name="sign" id="sms_sign"/>
          </div>
        </div>
        <?php endif; ?>
      <div class="form-group">
        <div class="label">
          <label></label>
        </div>
        <div class="field">
          <input type="hidden" name="id" value="<?php echo $config['id']; ?>">
          <button class="button bg-main icon-check-square-o" type="submit"> 提交</button>
        </div>
      </div>
      </form>
  </div>
</div>
</body>
</html>