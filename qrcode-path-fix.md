# 二维码扫码跳转空白问题修复

## 问题描述

**现象：**
扫描后台生成的二维码后，小程序打开空白页面

**二维码路径：**
```
/pages/goods/detail?id=39&date=2025-10-25&type=0
```

## 问题分析

### 1. 路径不匹配

**错误路径：** `/pages/goods/detail`（二维码生成的路径）
**正确路径：** `/packageA/goods/detail`（实际页面位置）

### 2. 路由拦截器无效

虽然小程序有路由拦截器（`common/fa.route.js`）可以自动转换路径：

```javascript
'/pages/goods/' -> '/packageA/goods/'
```

但是**扫码跳转不会经过路由拦截器**，所以无法自动转换。

路由拦截器只对以下方式的跳转生效：
- `uni.$u.route()`
- `uni.$u.routeIntercept()`
- 小程序内部的 `uni.navigateTo()` 等

**扫码跳转是系统行为，直接打开URL，不经过拦截器。**

## 解决方案

### 已修复：修改后台二维码生成路径

**修改文件：** `kcbg/application/admin/view/shop/goods/edit.html`

**修改位置：** 第510行

**修改前：**
```javascript
// 构建小程序页面URL
var url = '/pages/goods/detail?id=' + goodsId;
```

**修改后：**
```javascript
// 构建小程序页面URL（使用分包路径）
var url = '/packageA/goods/detail?id=' + goodsId;
```

### 现在生成的二维码路径格式

```
/packageA/goods/detail?id=39&date=2025-10-25&type=0
```

## 部署状态

- ✅ 后台文件已修改
- ✅ 已部署到服务器
- ✅ 缓存已清理

## 测试步骤

1. **后台操作**
   - 登录后台管理系统
   - 打开商品编辑页面
   - 找到"视频列表二维码"模块
   - 选择日期和分类
   - 点击"生成二维码"

2. **验证二维码**
   - 查看生成的小程序路径
   - 应该显示：`/packageA/goods/detail?id=XX&date=YYYY-MM-DD&type=X`

3. **扫码测试**
   - 使用微信扫描二维码
   - 应该正常打开商品详情页
   - 不再是空白页

## 其他需要修改的地方

如果后台还有其他生成小程序路径的地方，也需要检查并修改为分包路径：

### 常见路径映射

| 旧路径（pages） | 新路径（分包） | 说明 |
|----------------|---------------|------|
| `/pages/goods/detail` | `/packageA/goods/detail` | 商品详情 |
| `/pages/goods/goods` | `/packageA/goods/goods` | 商品列表 |
| `/pages/order/list` | `/packageB/order/list` | 订单列表 |
| `/pages/order/detail` | `/packageB/order/detail` | 订单详情 |
| `/pages/index/sycp` | `/packageC/index/sycp` | 溯源商品 |
| `/pages/index/sycpvideo` | `/packageC/index/sycpvideo` | 溯源视频 |
| `/pages/ingredientreplay/videos` | `/packageC/ingredientreplay/videos` | 配料回放视频 |

## 相关文档

- [小程序分包配置](../cwlsuniapp/pages.json) - 查看完整分包结构
- [路由拦截器](../cwlsuniapp/common/fa.route.js) - 小程序内部跳转的路径转换

## 注意事项

1. **后台生成的所有小程序路径都需要使用分包路径**
2. **扫码、分享链接等外部入口必须使用正确的分包路径**
3. **小程序内部跳转可以使用旧路径（会自动转换）**

---

**修复日期：** 2026-02-06
**修复版本：** v1.4.2
