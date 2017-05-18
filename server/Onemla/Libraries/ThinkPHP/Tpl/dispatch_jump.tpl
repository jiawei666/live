<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<link rel="stylesheet" type="text/css" href="__CSS__base.css">
<style type="text/css">
.jump_div { : 260px; height: 55px; position: absolute; left: 50%; top: 200px; margin-left: -130px;  }
.jump_icon { width: 55px; height: 55px; float: left; background: url(__IMAGES__system-img/admin-icons.png) no-repeat; margin-right: 14px;  }
.right .jump_icon { background-position: -131px 0;  }
.jump_txt {width: 250px;float: left;}
.right .jump_txt h2 { color: #009900; font-size: 20px; }
.jump_txt p {color: #666666;}
.jump_txt p a {text-decoration: underline; color: #9933cc; margin: 0 7px;}
.j_error .jump_icon { background-position: -131px -56px;  }
.j_error .jump_txt h2 { color: #cc0000; }
</style>
</head>
<body>
<div class="system-message">
<?php if(isset($message)) {?>
    <p class="success">
    <div class="jump_div right">
    <div class="jump_icon"></div>
    <div class="jump_txt">
    <h2><?php echo($message); ?></h2>
<?php }else{?>
    <div class="jump_div j_error">
    <div class="jump_icon"></div>
    <div class="jump_txt">
    <h2><?php echo($error); ?></h2>
    <p class="error"></p>
<?php }?>
<p>请稍等,页面会自动<a id="href" href="<?php echo($jumpUrl); ?>"> 跳转</a> ，等待时间:<span><b id="wait"><?php echo($waitSecond); ?></b>秒</span></p>
</div>
</div>

</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
