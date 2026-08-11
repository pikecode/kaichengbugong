<?php
$v = isset($_GET['v']) ? preg_replace('/[^0-9_]/', '', $_GET['v']) : '';
$parts = explode('_', $v);

if (count($parts) < 2 || !is_numeric($parts[0])) {
    http_response_code(400);
    exit('无效的链接');
}

$id       = intval($parts[0]);
$date_s   = $parts[1]; // YYMMDD
$type     = isset($parts[2]) ? intval($parts[2]) : 0;

// YYMMDD → YYYY-MM-DD
$full_date = '20' . substr($date_s, 0, 2) . '-' . substr($date_s, 2, 2) . '-' . substr($date_s, 4, 2);
$mp_path   = json_encode('/packageA/goods/detail?id=' . $id . '&date=' . $full_date . '&type=' . $type);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title>正在跳转...</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f5f7fa;font-family:-apple-system,BlinkMacSystemFont,sans-serif}
  .card{background:#fff;border-radius:16px;padding:40px 32px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.08);max-width:320px;width:90%}
  .icon{font-size:48px;margin-bottom:16px}
  .title{font-size:18px;font-weight:600;color:#1a1a1a;margin-bottom:8px}
  .tip{font-size:13px;color:#999;line-height:1.6}
</style>
</head>
<body>
<div class="card">
  <div class="icon">🛍️</div>
  <div class="title">正在跳转到小程序</div>
  <div class="tip">请在微信中打开此链接，即可跳转查看商品视频</div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
<script>
wx.miniProgram.navigateTo({ url: <?= $mp_path ?> });
</script>
</body>
</html>
