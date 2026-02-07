# 日期地址配置功能 - 问题修复记录

## 修复时间
2026-02-06

## 问题汇总

### 问题1：后台日期选择器 ✅ 已修复
**问题描述：** 添加日期地址时，日期输入框不能使用日期选择控件，只能手动输入

**解决方案：**
- 将 `Layer.prompt()` 改为 `Layer.open()` 自定义弹窗
- 使用 `<input type="date">` 日期选择器
- 用户可以直接点击日历图标选择日期

### 问题2：不能编辑已添加的配置 ✅ 已修复
**问题描述：** 添加日期地址后，无法修改，只能删除重新添加

**解决方案：**
- 新增 `editDateUrl(date)` 编辑函数
- 在操作列添加"编辑"按钮
- 支持修改日期和地址
- 如果修改日期，自动删除旧配置并创建新配置

### 问题3：刷新页面后配置丢失 ✅ 已修复
**问题描述：** 详情页再渲染时，看不到之前保存的日期地址列表

**解决方案：**
- 增强 `initDateUrls()` 初始化逻辑
- 添加更严格的空值检查和类型检查
- 添加错误日志，方便调试
- 确保页面加载时正确解析并显示已保存的JSON数据

### 问题4：jQuery依赖错误 ✅ 已修复
**问题描述：** 控制台报错 `Uncaught ReferenceError: $ is not defined`

**解决方案：**
- 将 `$(function(){...})` 改为原生JavaScript
- 使用 `document.addEventListener('DOMContentLoaded', ...)`
- 添加 `document.readyState` 判断，兼容DOM已加载的情况

### 问题5：小程序端不显示配置的地址 ✅ 已修复
**问题描述：** 后台维护了日期地址，但小程序端还是显示"暂无配料回放内容"

**根本原因：**
- API返回数据时，`visible()` 方法限制了可见字段
- 字段列表中包含了 `traceability_url`，但遗漏了 `date_urls`

**解决方案：**
- 修改 `kcbg/addons/shop/controller/api/Goods.php` 第217行
- 在 `visible()` 字段列表末尾添加 `,date_urls`
- 现在API会正确返回 `date_urls` 字段给小程序

**修改代码：**
```php
// 修改前
$row->visible(explode(',', 'id,title,subtitle,...,traceability_url'));

// 修改后
$row->visible(explode(',', 'id,title,subtitle,...,traceability_url,date_urls'));
```

---

## 测试步骤

### 后台测试（已通过）

1. **添加测试**
   - 登录后台管理系统
   - 商品管理 → 编辑商品
   - 找到"日期地址配置"模块
   - 点击"添加日期地址"
   - 使用日期选择器选择日期（如：2026-02-06）
   - 输入地址（如：/pages/ingredientreplay/videos?id=10）
   - 点击确定
   - ✅ 列表中显示新添加的配置

2. **编辑测试**
   - 点击某条记录的"编辑"按钮
   - 修改日期或地址
   - 点击保存
   - ✅ 配置更新成功

3. **持久化测试**
   - 添加2-3条配置
   - 保存商品
   - 刷新页面
   - ✅ 配置仍然显示

4. **删除测试**
   - 点击某条记录的"删除"按钮
   - 确认删除
   - ✅ 记录被移除

### 小程序测试（需要用户测试）

1. **前置条件**
   - 在后台为某个商品配置日期地址（如：2026-02-06 → /pages/ingredientreplay/videos?id=10）
   - 保存商品
   - 确保该日期有生产数据（datearr包含该日期）

2. **测试步骤**
   - 打开商品详情页
   - 选择已配置的日期（2026-02-06）
   - 点击"配料回放"tab
   - ✅ 应该显示入口按钮，标题为"查看本日配料回放"，副标题显示日期
   - 点击入口按钮
   - ✅ 应该跳转到配置的地址

3. **未配置日期测试**
   - 选择未配置地址的日期
   - 点击"配料回放"tab
   - ✅ 应该显示"当前日期暂无配料回放内容"提示

---

## 部署状态

### ✅ 已部署
- [x] 数据库字段 `date_urls` 已添加
- [x] 后台管理页面已部署（含所有修复）
- [x] API接口已修复（添加 date_urls 到返回字段）
- [x] 缓存已清理

### ⏳ 待部署
- [ ] 小程序前端代码（需要编译上传）

---

## 技术细节

### API返回数据示例

修复后，商品详情API (`/addons/shop/goods/detail`) 会正确返回：

```json
{
  "code": 1,
  "data": {
    "id": 123,
    "title": "商品名称",
    "traceability_url": "/pages/index/sycpvideo?id=8",
    "date_urls": "{\"2026-02-06\":\"/pages/ingredientreplay/videos?id=10\",\"2026-02-07\":\"/packageA/goods/detail?id=20\"}",
    "ri": ["2026-02-06", "2026-02-07", "2026-02-08"],
    ...
  }
}
```

### 小程序计算属性逻辑

```javascript
computed: {
  currentDateUrl() {
    // 1. 检查 date_urls 字段是否存在
    if (!this.info.date_urls || !this.datearr[this.riindex]) {
      return null;
    }

    try {
      // 2. 获取当前选中日期
      const selectedDate = this.datearr[this.riindex].riqi; // "2026-02-06"

      // 3. 解析JSON
      const dateUrls = JSON.parse(this.info.date_urls);

      // 4. 返回匹配的URL，如果不存在返回null
      return dateUrls[selectedDate] || null;
    } catch (e) {
      console.error('解析日期地址失败', e);
      return null;
    }
  }
}
```

---

## 回滚方案

如果出现问题需要回滚：

```bash
# 1. 恢复后台文件
scp /backup/20260206/goods_edit.html.bak root@8.154.32.2:/www/wwwroot/cwls.turuifanxin.cn/application/admin/view/shop/goods/edit.html

# 2. 恢复API文件（需要先备份原文件）
scp /backup/20260206/Goods.php.bak root@8.154.32.2:/www/wwwroot/cwls.turuifanxin.cn/addons/shop/controller/api/Goods.php

# 3. 清理缓存
ssh root@8.154.32.2 "cd /www/wwwroot/cwls.turuifanxin.cn/runtime/ && rm -rf cache/* temp/*"

# 4. 数据库回滚（如果需要）
mysql -ucwls -pewXHjm77twRm3xL2 cwls -e "ALTER TABLE fa_shop_goods DROP COLUMN date_urls;"
```

---

## 下一步

1. **小程序端测试**
   - 由于小程序代码已修改但未重新编译上传
   - 需要用户重新编译并上传小程序
   - 测试日期地址跳转功能

2. **完整测试**
   - 配置日期地址
   - 小程序选择日期
   - 验证入口按钮显示
   - 点击跳转验证

---

**修复完成时间：** 2026-02-06
**修复版本：** v1.4.1（修复版）
