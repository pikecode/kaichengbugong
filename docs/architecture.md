# 系统架构

## 整体架构

本系统采用**前后端分离**架构，由两个独立项目组成：

```
┌─────────────────────────────────────────────────────────────┐
│                        用户层                                │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │微信小程序│  │  H5网页  │  │ Android  │  │   iOS    │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ HTTP/HTTPS
                         │
┌────────────────────────▼────────────────────────────────────┐
│                    前端应用层                                │
│                  cwlsuniapp/                                 │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  UniApp Framework (Vue.js)                          │   │
│  │  ├─ Pages (页面组件)                                 │   │
│  │  ├─ Components (公共组件)                            │   │
│  │  ├─ Store (Vuex状态管理)                             │   │
│  │  ├─ Common (工具函数/API封装)                        │   │
│  │  └─ uView UI (UI组件库)                              │   │
│  └─────────────────────────────────────────────────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ RESTful API
                         │
┌────────────────────────▼────────────────────────────────────┐
│                    后端应用层                                │
│                     kcbg/                                    │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  FastAdmin Framework (ThinkPHP 5.x)                 │   │
│  │  ├─ Admin Module (管理后台)                          │   │
│  │  ├─ API Module (RESTful接口)                         │   │
│  │  ├─ Index Module (前台页面)                          │   │
│  │  └─ Common Module (公共资源)                         │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  插件系统 (Addons)                                    │   │
│  │  ├─ shop (电商插件 v1.2.5)                           │   │
│  │  ├─ epay (支付网关 v1.3.6)                           │   │
│  │  ├─ third (第三方登录 v1.4.4)                        │   │
│  │  └─ summernote (富文本编辑器 v1.1.2)                 │   │
│  └─────────────────────────────────────────────────────┘   │
└────────────────────────┬────────────────────────────────────┘
                         │
                         │ PDO/MySQL
                         │
┌────────────────────────▼────────────────────────────────────┐
│                    数据持久层                                │
│                  MySQL 5.7+                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  数据库: cwls (utf8mb4)                              │   │
│  │  ├─ 管理员表 (fa_admin, fa_auth_*)                   │   │
│  │  ├─ 用户表 (fa_user, fa_user_*)                      │   │
│  │  ├─ 商品表 (fa_shop_goods, fa_shop_sku)             │   │
│  │  ├─ 订单表 (fa_shop_order, fa_shop_order_goods)     │   │
│  │  ├─ 溯源表 (fa_traceability, fa_traceability_goods) │   │
│  │  └─ 系统表 (fa_config, fa_attachment, fa_category)  │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

## 技术栈详解

### 前端技术栈 (cwlsuniapp/)

| 层级 | 技术 | 版本 | 用途 |
|------|------|------|------|
| 框架 | UniApp | - | 跨平台应用开发框架 |
| 核心 | Vue.js | 2.x | 渐进式JavaScript框架 |
| UI库 | uView UI | - | UniApp UI组件库 |
| 状态管理 | Vuex | - | 集中式状态管理 |
| 构建工具 | HBuilderX | - | UniApp官方IDE |
| 包管理 | npm | - | 依赖管理 |

**支持平台**:
- 微信小程序 (主要平台)
- H5网页
- Android App
- iOS App
- 支付宝小程序
- 百度小程序
- 头条小程序

### 后端技术栈 (kcbg/)

| 层级 | 技术 | 版本 | 用途 |
|------|------|------|------|
| 框架 | FastAdmin | - | 快速开发框架 |
| 核心 | ThinkPHP | 5.x | PHP MVC框架 |
| 语言 | PHP | 7.1+ | 服务端语言 |
| 数据库 | MySQL | 5.7+ | 关系型数据库 |
| 前端 | AdminLTE | - | 后台UI框架 |
| UI库 | Bootstrap | 3.3.7 | CSS框架 |
| JS库 | jQuery | 2.1.4 | JavaScript库 |
| 模块加载 | RequireJS | - | AMD模块加载器 |
| 图表 | ECharts | - | 数据可视化 |
| 表格 | Bootstrap Table | - | 数据表格组件 |

**核心依赖**:
- `topthink/framework` - ThinkPHP核心
- `topthink/think-captcha` - 验证码
- `topthink/think-queue` - 队列系统
- `karsonzhang/fastadmin-addons` - 插件系统
- `overtrue/wechat` - 微信SDK
- `phpoffice/phpspreadsheet` - Excel操作

## 模块架构

### 后端模块结构

```
kcbg/
├── application/              # 应用目录
│   ├── admin/               # 后台管理模块
│   │   ├── controller/      # 控制器 (299个文件)
│   │   ├── model/           # 模型
│   │   ├── view/            # 视图模板
│   │   └── command/         # CLI命令
│   ├── api/                 # API接口模块
│   │   ├── controller/      # API控制器
│   │   └── model/           # API模型
│   ├── index/               # 前台模块
│   │   ├── controller/      # 前台控制器
│   │   └── view/            # 前台视图
│   └── common/              # 公共模块
│       ├── model/           # 公共模型
│       └── library/         # 公共类库
├── addons/                  # 插件目录
│   ├── shop/               # 电商插件 (45+模型)
│   ├── epay/               # 支付插件
│   ├── third/              # 第三方登录
│   └── summernote/         # 富文本编辑器
├── public/                  # 公共资源
│   ├── index.php           # 主入口
│   ├── xSZaYJEibq.php     # 后台入口
│   └── assets/             # 静态资源
├── thinkphp/               # ThinkPHP框架
└── vendor/                 # Composer依赖
```

### 前端模块结构

```
cwlsuniapp/
├── pages/                   # 页面目录
│   ├── index/              # 首页
│   ├── category/           # 分类
│   ├── goods/              # 商品
│   ├── cart/               # 购物车
│   ├── order/              # 订单
│   ├── my/                 # 个人中心
│   ├── login/              # 登录注册
│   ├── address/            # 地址管理
│   ├── coupon/             # 优惠券
│   ├── score/              # 积分
│   ├── video/              # 视频播放
│   └── ...                 # 其他页面
├── components/             # 公共组件
├── common/                 # 公共资源
│   ├── api/               # API封装
│   ├── utils/             # 工具函数
│   └── config/            # 配置文件
├── store/                  # Vuex状态管理
├── static/                 # 静态资源
│   ├── images/            # 图片
│   ├── tabbar/            # 底部导航图标
│   └── ...
├── uview-ui/              # uView UI组件库
├── uni_modules/           # uni_modules插件
├── App.vue                # 应用入口
├── main.js                # 主入口文件
├── pages.json             # 页面配置
└── manifest.json          # 应用配置
```

## 数据流

### 用户操作流程

```
用户操作 (小程序/H5/App)
    ↓
前端页面 (pages/)
    ↓
API调用 (common/api/)
    ↓
HTTP请求
    ↓
后端API (application/api/)
    ↓
业务逻辑处理
    ↓
数据库操作 (Model)
    ↓
MySQL数据库 (cwls)
    ↓
返回数据
    ↓
前端渲染
    ↓
用户界面更新
```

### 管理员操作流程

```
管理员登录 (xSZaYJEibq.php)
    ↓
后台界面 (application/admin/view/)
    ↓
控制器处理 (application/admin/controller/)
    ↓
模型操作 (application/admin/model/)
    ↓
数据库操作
    ↓
返回结果
    ↓
后台界面更新
```

## 插件架构

FastAdmin采用插件化架构，支持动态加载和卸载插件：

```
插件系统
├── 插件管理器 (Addon Manager)
│   ├── 安装插件
│   ├── 卸载插件
│   ├── 启用/禁用
│   └── 配置管理
├── 插件钩子 (Hooks)
│   ├── 控制器钩子
│   ├── 视图钩子
│   └── 模型钩子
└── 插件列表
    ├── shop (电商)
    ├── epay (支付)
    ├── third (第三方登录)
    └── summernote (编辑器)
```

## 安全架构

### 认证授权

- **后台**: 基于Session的RBAC权限控制
- **API**: Token认证机制
- **小程序**: 微信授权 + Token

### 数据安全

- 密码加密存储 (Hash)
- SQL注入防护 (PDO预处理)
- XSS防护 (输入过滤)
- CSRF防护 (Token验证)

## 部署架构

### 推荐部署方案

```
┌─────────────────────────────────────────┐
│         负载均衡 (Nginx)                 │
└──────────┬──────────────────────────────┘
           │
    ┌──────┴──────┐
    │             │
┌───▼───┐    ┌───▼───┐
│ Web 1 │    │ Web 2 │  (PHP-FPM)
└───┬───┘    └───┬───┘
    │             │
    └──────┬──────┘
           │
    ┌──────▼──────┐
    │   MySQL     │  (主从复制)
    └─────────────┘
```

### 环境要求

**后端**:
- PHP >= 7.1
- MySQL >= 5.7
- Nginx/Apache
- Composer
- PHP扩展: PDO, mbstring, openssl, curl, gd

**前端**:
- HBuilderX (开发)
- Node.js (构建)
- 微信开发者工具 (小程序调试)

## 性能优化

### 后端优化
- 数据库索引优化
- Redis缓存 (可选)
- 队列系统 (异步任务)
- CDN静态资源加速

### 前端优化
- 图片懒加载
- 分包加载 (小程序)
- 本地缓存
- 请求防抖/节流
