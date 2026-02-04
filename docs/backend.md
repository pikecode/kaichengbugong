# 后端项目文档 (kcbg/)

## 项目概述

基于 FastAdmin + ThinkPHP 5.x 的电商溯源系统后端，提供管理后台和 RESTful API 接口。

## 目录结构

```
kcbg/
├── application/              # 应用目录
│   ├── admin/               # 后台管理模块 (299个PHP文件)
│   │   ├── controller/      # 控制器
│   │   │   ├── Dashboard.php        # 仪表盘
│   │   │   ├── Category.php         # 分类管理
│   │   │   ├── Addon.php            # 插件管理
│   │   │   ├── Auth.php             # 权限管理
│   │   │   ├── Traceability.php     # 溯源管理
│   │   │   └── ...
│   │   ├── model/           # 模型
│   │   │   ├── Traceability.php     # 溯源模型
│   │   │   ├── TraceabilityGoods.php # 溯源商品
│   │   │   └── TraceabilityGoodsVideo.php # 溯源视频
│   │   ├── view/            # 视图模板
│   │   ├── command/         # CLI命令
│   │   │   └── Install.php  # 安装命令
│   │   └── lang/            # 语言包
│   ├── api/                 # API接口模块
│   │   ├── controller/      # API控制器
│   │   │   ├── User.php     # 用户接口
│   │   │   ├── Token.php    # Token认证
│   │   │   ├── Sms.php      # 短信接口
│   │   │   ├── Ems.php      # 邮件接口
│   │   │   └── ...
│   │   └── model/           # API模型
│   ├── index/               # 前台模块
│   │   ├── controller/      # 前台控制器
│   │   └── view/            # 前台视图
│   ├── common/              # 公共模块
│   │   ├── model/           # 公共模型
│   │   │   ├── User.php     # 用户模型
│   │   │   ├── Config.php   # 配置模型
│   │   │   ├── Area.php     # 地区模型
│   │   │   ├── Attachment.php # 附件模型
│   │   │   └── Category.php # 分类模型
│   │   └── library/         # 公共类库
│   ├── config.php           # 应用配置
│   ├── database.php         # 数据库配置
│   ├── route.php            # 路由配置
│   └── extra/               # 扩展配置
│       └── addons.php       # 插件配置
├── addons/                  # 插件目录
│   ├── shop/               # 电商插件 (v1.2.5)
│   ├── epay/               # 支付插件 (v1.3.6)
│   ├── third/              # 第三方登录 (v1.4.4)
│   └── summernote/         # 富文本编辑器 (v1.1.2)
├── public/                  # 公共资源目录
│   ├── index.php           # 主入口文件
│   ├── xSZaYJEibq.php     # 后台入口文件 (混淆命名)
│   ├── install.php         # 安装向导
│   ├── assets/             # 静态资源
│   │   ├── css/            # 样式文件
│   │   ├── js/             # JavaScript文件
│   │   ├── img/            # 图片资源
│   │   └── libs/           # 第三方库
│   └── uploads/            # 上传文件目录
├── extend/                  # 扩展类库
├── thinkphp/               # ThinkPHP框架核心
├── vendor/                 # Composer依赖
├── runtime/                # 运行时目录 (缓存、日志)
├── composer.json           # Composer配置
├── .env.sample             # 环境变量示例
├── cwls.sql               # 数据库SQL文件 (6770行)
└── README.md              # 项目说明
```

## 核心模块

### 1. Admin 模块 (后台管理)

**主要功能**:
- 仪表盘统计
- 权限管理 (RBAC)
- 用户管理
- 分类管理
- 内容管理
- 插件管理
- 系统配置
- 溯源管理

**权限系统**:
```php
// 基于角色的访问控制 (RBAC)
fa_admin          // 管理员表
fa_admin_log      // 管理员日志
fa_auth_group     // 角色组
fa_auth_rule      // 权限规则
fa_auth_group_access // 用户-角色关联
```

**关键控制器**:
- `Dashboard.php` - 仪表盘
- `Auth.php` - 权限管理
- `Category.php` - 分类管理
- `Addon.php` - 插件管理
- `Traceability.php` - 溯源管理
- `TraceabilityGoods.php` - 溯源商品
- `TraceabilityGoodsVideo.php` - 溯源视频

### 2. API 模块 (接口服务)

**主要功能**:
- 用户认证 (Token)
- 用户注册/登录
- 短信验证码
- 邮件验证码
- 数据接口

**认证流程**:
```
1. 用户登录 → POST /api/user/login
2. 返回 Token
3. 后续请求携带 Token → Header: token: xxx
4. 服务端验证 Token
```

**关键接口**:
- `User.php` - 用户相关接口
- `Token.php` - Token认证
- `Sms.php` - 短信服务
- `Ems.php` - 邮件服务
- `Validate.php` - 验证服务

### 3. Common 模块 (公共资源)

**公共模型**:
- `User.php` - 用户模型
- `Config.php` - 配置模型
- `Area.php` - 地区模型
- `Attachment.php` - 附件模型
- `Category.php` - 分类模型
- `Version.php` - 版本管理

## 插件系统

### Shop 插件 (v1.2.5) - 简单商城

**目录结构**:
```
addons/shop/
├── controller/              # 控制器 (前台+后台)
│   ├── Goods.php           # 商品管理
│   ├── Order.php           # 订单管理
│   ├── Cart.php            # 购物车
│   ├── Checkout.php        # 结算
│   ├── Payment.php         # 支付
│   ├── Category.php        # 分类
│   └── Search.php          # 搜索
├── model/                   # 模型 (45+个)
│   ├── OrderGoods.php      # 订单商品
│   ├── Carts.php           # 购物车
│   ├── Address.php         # 收货地址
│   ├── Brand.php           # 品牌
│   ├── Sku.php             # SKU规格
│   ├── Coupon.php          # 优惠券
│   └── ...
├── view/                    # 视图模板
├── cwlsuniapp/             # UniApp前端集成
└── config.php              # 插件配置
```

**核心功能**:
- 商品管理 (分类、品牌、SKU)
- 购物车系统
- 订单管理 (下单、支付、发货、售后)
- 优惠券系统 (折扣券、满减券)
- 物流追踪 (快递API集成)
- 全文搜索
- 敏感内容过滤 (百度AI)
- 地区管理

**数据表** (部分):
```
fa_shop_goods              // 商品表
fa_shop_goods_sku          // SKU表
fa_shop_order              // 订单表
fa_shop_order_goods        // 订单商品表
fa_shop_carts              // 购物车表
fa_shop_address            // 收货地址表
fa_shop_coupon             // 优惠券表
fa_shop_coupon_user        // 用户优惠券表
fa_shop_brand              // 品牌表
fa_shop_category           // 分类表
```

### Epay 插件 (v1.3.6) - 支付网关

**支持支付方式**:
- 支付宝 (Alipay)
- 微信支付 (WeChat Pay)

**主要功能**:
- 订单创建
- 支付回调处理
- 退款处理
- 支付状态查询

### Third 插件 (v1.4.4) - 第三方登录

**支持平台**:
- QQ登录
- 微信登录
- 微博登录

**OAuth流程**:
```
1. 用户点击第三方登录
2. 跳转到第三方授权页面
3. 用户授权
4. 回调获取用户信息
5. 绑定或创建本地账号
6. 登录成功
```

### Summernote 插件 (v1.1.2) - 富文本编辑器

**功能**:
- 富文本编辑
- 图片上传
- 视频嵌入
- 代码高亮

## 数据库设计

### 核心表结构

**管理员相关**:
```sql
fa_admin              -- 管理员表
fa_admin_log          -- 管理员操作日志
fa_auth_group         -- 角色组
fa_auth_rule          -- 权限规则
fa_auth_group_access  -- 用户角色关联
```

**用户相关**:
```sql
fa_user               -- 用户表
fa_user_group         -- 用户组
fa_user_rule          -- 用户权限
fa_user_score_log     -- 积分日志
fa_user_money_log     -- 余额日志
```

**系统相关**:
```sql
fa_config             -- 系统配置
fa_attachment         -- 附件表
fa_category           -- 分类表
fa_area               -- 地区表
fa_version            -- 版本管理
```

**溯源相关**:
```sql
fa_traceability              -- 溯源分类
fa_traceability_goods        -- 溯源商品
fa_traceability_goods_video  -- 溯源视频
```

### 表前缀

所有数据表使用 `fa_` 前缀，可在配置文件中修改。

## 配置文件

### 应用配置 (application/config.php)

```php
return [
    'app_debug' => true,              // 调试模式
    'app_trace' => false,             // 应用Trace
    'default_timezone' => 'Asia/Shanghai', // 时区
    'url_route_on' => true,           // 路由开关
    'url_route_must' => false,        // 路由必须
    // ... 更多配置
];
```

### 数据库配置 (application/database.php)

```php
return [
    'type'            => 'mysql',
    'hostname'        => '127.0.0.1',
    'database'        => 'cwls',      // 数据库名
    'username'        => 'root',
    'password'        => '',
    'hostport'        => '3306',
    'charset'         => 'utf8mb4',
    'prefix'          => 'fa_',       // 表前缀
    // ... 更多配置
];
```

### 环境变量 (.env)

```ini
APP_DEBUG = true
DATABASE_HOSTNAME = 127.0.0.1
DATABASE_DATABASE = cwls
DATABASE_USERNAME = root
DATABASE_PASSWORD =
DATABASE_PREFIX = fa_
```

## 入口文件

### 主入口 (public/index.php)

```php
// 前台访问入口
// URL: http://domain.com/
```

### 后台入口 (public/xSZaYJEibq.php)

```php
// 后台管理入口 (混淆命名)
// URL: http://domain.com/xSZaYJEibq.php
// 建议定期修改文件名以提高安全性
```

### 安装入口 (public/install.php)

```php
// 安装向导
// URL: http://domain.com/install.php
// 安装完成后应删除此文件
```

## API 接口规范

### 请求格式

```http
POST /api/user/login HTTP/1.1
Host: domain.com
Content-Type: application/json

{
    "username": "user@example.com",
    "password": "password123"
}
```

### 响应格式

```json
{
    "code": 1,
    "msg": "登录成功",
    "data": {
        "userinfo": {
            "id": 1,
            "username": "user",
            "nickname": "用户昵称"
        },
        "token": "xxxxxxxxxxxxxx"
    },
    "time": 1234567890
}
```

### 状态码

- `code: 1` - 成功
- `code: 0` - 失败
- `code: -1` - 未登录
- `code: -2` - 无权限

## 开发指南

### 创建新控制器

```php
<?php
namespace app\admin\controller;

use app\common\controller\Backend;

class Example extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Example');
    }

    public function index()
    {
        // 列表页面
        if ($this->request->isAjax()) {
            // AJAX请求返回数据
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model->where($where)->order($sort, $order)->paginate($limit);
            return json(['total' => $list->total(), 'rows' => $list->items()]);
        }
        return $this->view->fetch();
    }
}
```

### 创建新模型

```php
<?php
namespace app\common\model;

use think\Model;

class Example extends Model
{
    // 表名
    protected $name = 'example';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = ['status_text'];

    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }
}
```

### 创建新插件

```bash
# 在 addons/ 目录下创建插件目录
addons/myplugin/
├── controller/
├── model/
├── view/
├── config.php      # 插件配置
└── info.ini        # 插件信息
```

## 常用命令

### Composer 依赖管理

```bash
# 安装依赖
composer install

# 更新依赖
composer update

# 添加依赖
composer require vendor/package
```

### ThinkPHP 命令

```bash
# 清除缓存
php think clear

# 生成类库映射文件
php think optimize:autoload

# 生成数据表字段缓存
php think optimize:schema

# 生成路由缓存
php think optimize:route

# 生成配置缓存
php think optimize:config
```

## 安全建议

### 生产环境配置

1. **关闭调试模式**:
```php
// application/config.php
'app_debug' => false,
```

2. **修改后台入口文件名**:
```bash
mv public/xSZaYJEibq.php public/your_custom_name.php
```

3. **修改数据库密码**:
```php
// application/database.php
'password' => 'strong_password_here',
```

4. **删除安装文件**:
```bash
rm public/install.php
```

5. **设置目录权限**:
```bash
chmod 755 public/
chmod 777 runtime/
chmod 777 public/uploads/
```

### 防护措施

- SQL注入防护: 使用PDO预处理
- XSS防护: 输入过滤和输出转义
- CSRF防护: Token验证
- 文件上传: 类型和大小限制
- 密码加密: 使用Hash算法

## 性能优化

### 缓存配置

```php
// application/config.php
'cache' => [
    'type'   => 'redis',
    'host'   => '127.0.0.1',
    'port'   => 6379,
    'prefix' => 'think:',
    'expire' => 0,
],
```

### 数据库优化

- 添加索引
- 使用查询缓存
- 分表分库
- 读写分离

### 静态资源优化

- 使用CDN
- 开启Gzip压缩
- 合并CSS/JS文件
- 图片压缩

## 日志管理

### 日志位置

```
runtime/log/
├── 202402/
│   ├── 04.log      # 按日期分割
│   └── 04_sql.log  # SQL日志
```

### 日志级别

- `log` - 一般日志
- `error` - 错误日志
- `info` - 信息日志
- `sql` - SQL日志
- `debug` - 调试日志

## 故障排查

### 常见问题

1. **500错误**: 检查PHP错误日志和应用日志
2. **数据库连接失败**: 检查数据库配置和权限
3. **权限不足**: 检查目录权限 (runtime/, uploads/)
4. **路由不存在**: 清除路由缓存
5. **插件无法安装**: 检查插件目录权限

### 调试模式

```php
// 开启调试模式查看详细错误信息
'app_debug' => true,
'app_trace' => true,
```
