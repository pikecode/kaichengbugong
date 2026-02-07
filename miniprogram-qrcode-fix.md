# 微信小程序码生成功能修复

## 问题描述

**原问题：**
扫描后台生成的二维码无法跳转到小程序，显示空白页面。

**根本原因：**
1. 原代码使用 `QRCode.js` 生成普通二维码，不是微信小程序码
2. 普通二维码扫描后无法识别为小程序路径
3. 需要调用微信API生成真正的小程序码

## 解决方案

### 1. 修改前端代码

**文件：** `kcbg/application/admin/view/shop/goods/edit.html`

**修改内容：**
- 不再使用 `QRCode.js` 生成普通二维码
- 改为调用后端API生成真正的微信小程序码
- 使用 AJAX 异步请求，显示加载状态

**关键代码：**
```javascript
function generateQRCode() {
    // ...验证逻辑...

    // 构建小程序页面路径（使用分包路径）
    var page = 'packageA/goods/detail';

    // 构建scene参数（小程序码的参数，最多32个字符）
    var scene = 'id=' + goodsId + '&date=' + date + '&type=' + type;

    // 调用后端API生成小程序码
    $.ajax({
        url: '/addons/shop/ajax/getQRCode',
        type: 'POST',
        data: {
            page: page,
            scene: scene
        },
        success: function(res) {
            if (res.code == 1) {
                // 显示小程序码图片
                document.getElementById('qrcode').innerHTML = '<img src="' + res.data + '" />';
            }
        }
    });
}
```

### 2. 新增后端API

**文件：** `kcbg/application/admin/controller/shop/Ajax.php`

**新增方法：** `getQRCode()`

**功能：**
- 接收页面路径（page）和参数（scene）
- 调用微信API生成小程序码
- 保存小程序码图片到服务器
- 返回图片URL

**关键代码：**
```php
public function getQRCode()
{
    $page = $this->request->post('page', '');
    $scene = $this->request->post('scene', '');

    // 调用微信API生成小程序码
    $mini = new \addons\shop\library\message\Mini();
    $fileStream = $mini->getWxCodeUnlimit([
        'scene'       => $scene,
        'page'        => $page,
        'env_version' => 'release', // 正式版
        'check_path'  => false,
        'width'       => 280
    ]);

    // 保存图片并返回URL
    // ...
}
```

## 微信小程序码 vs 普通二维码

### 普通二维码（原方案）
- ❌ 使用 `QRCode.js` 在前端生成
- ❌ 只是普通的文本二维码
- ❌ 扫描后无法识别为小程序
- ❌ 无法直接跳转

### 微信小程序码（新方案）
- ✅ 调用微信官方API生成
- ✅ 真正的小程序码
- ✅ 扫描后自动识别为小程序
- ✅ 直接跳转到指定页面

## 参数说明

### page（页面路径）
小程序的页面路径，必须使用分包路径：
```
packageA/goods/detail
```

**注意：** 不要加前缀斜杠 `/`

### scene（参数）
小程序页面的参数，格式为URL query string：
```
id=39&date=2026-02-12&type=0
```

**限制：** 最多32个可见字符

### 小程序接收参数

在小程序页面的 `onLoad` 中接收参数：

```javascript
onLoad(options) {
    // 如果是扫码进入，参数在 options.scene 中
    if (options.scene) {
        // scene 格式：id=39&date=2026-02-12&type=0
        const params = this.parseScene(options.scene);
        this.id = params.id;
        this.date = params.date;
        this.type = params.type;
    } else {
        // 普通跳转，参数直接在 options 中
        this.id = options.id;
        this.date = options.date;
        this.type = options.type;
    }
}

parseScene(scene) {
    const params = {};
    scene.split('&').forEach(item => {
        const [key, value] = item.split('=');
        params[key] = value;
    });
    return params;
}
```

## 部署状态

- ✅ 前端文件已修改（edit.html）
- ✅ 后端API已创建（Ajax.php）
- ✅ 已部署到服务器
- ✅ 缓存已清理
- ✅ 上传目录已创建（/public/uploads/qrcode/）

## 测试步骤

### 1. 后台生成小程序码

1. 登录后台管理系统
2. 打开商品编辑页面
3. 找到"视频列表二维码"模块
4. 选择日期：2026-02-12
5. 选择分类：生产全程回放（type=0）
6. 点击"生成二维码"
7. 等待生成（会显示加载中）
8. 生成成功后显示小程序码图片

### 2. 扫码测试

1. 使用微信扫描生成的小程序码
2. 应该识别为"凯程不公小程序"（你的小程序名称）
3. 点击进入小程序
4. 应该直接打开商品详情页
5. 商品ID=39，日期=2026-02-12，type=0

### 3. 小程序参数接收测试

在商品详情页 `onLoad` 中添加调试代码：

```javascript
onLoad(options) {
    console.log('options:', options);
    console.log('scene:', options.scene);

    // 处理scene参数...
}
```

## 常见问题

### Q1: 生成失败，提示"生成失败: invalid page"

**原因：** 页面路径不正确或页面不存在

**解决：**
1. 检查pages.json中是否配置了该页面
2. 确认使用分包路径：`packageA/goods/detail`
3. 不要加前缀斜杠

### Q2: 生成失败，提示"access_token invalid"

**原因：** 小程序配置未正确设置或access_token过期

**解决：**
1. 检查后台小程序配置（AppID、AppSecret）
2. 确认小程序已发布
3. 重新获取access_token

### Q3: 扫码后参数丢失

**原因：** 小程序未正确解析scene参数

**解决：**
在商品详情页添加scene参数解析逻辑（见上方"小程序接收参数"）

### Q4: scene参数超过32字符

**原因：** 参数过长

**解决方案：**
1. 使用短参数名：`id` 而不是 `goods_id`
2. 缩短日期格式：`2026-02-12` → `20260212`
3. 如果还是超长，考虑生成短链接或使用数据库映射

## 微信小程序码API文档

官方文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html

**接口：** `wxacode.getUnlimited`

**特点：**
- 数量不限
- 适合需要大量二维码的场景
- 通过scene参数传递数据

**参数限制：**
- scene：最多32个可见字符
- page：必须是已发布的小程序页面

---

**修复日期：** 2026-02-06
**版本：** v1.5.0
