# 前端项目文档 (cwlsuniapp/)

## 项目概述

基于 UniApp + Vue.js 的跨平台电商应用，支持微信小程序、H5、Android/iOS App 等多端运行。

## 项目信息

- **项目名称**: 商城
- **版本**: v1.2.0 (versionCode: 3)
- **AppID**: __UNI__8303F50
- **微信小程序 AppID**: wx3a708b76e9f65456
- **UI框架**: uView UI
- **开发工具**: HBuilderX

## 目录结构

```
cwlsuniapp/
├── pages/                   # 页面目录
│   ├── index/              # 首页模块
│   │   ├── index.vue       # 首页
│   │   ├── my.vue          # 我的
│   │   ├── sycp.vue        # 溯源产品
│   │   └── sycpvideo.vue   # 溯源产品视频
│   ├── category/           # 分类模块
│   │   └── index.vue       # 分类页
│   ├── goods/              # 商品模块
│   │   ├── goods.vue       # 商品列表
│   │   ├── detail.vue      # 商品详情
│   │   └── order.vue       # 提交订单
│   ├── cart/               # 购物车模块
│   │   └── cart.vue        # 购物车
│   ├── order/              # 订单模块
│   │   ├── list.vue        # 订单列表
│   │   ├── detail.vue      # 订单详情
│   │   ├── payment.vue     # 立即支付
│   │   ├── apply.vue       # 申请售后
│   │   ├── aftersale.vue   # 查看售后
│   │   └── logistics.vue   # 查看物流
│   ├── my/                 # 个人中心模块
│   │   ├── my.vue          # 个人中心
│   │   ├── profile.vue     # 个人资料
│   │   └── collect.vue     # 我的收藏
│   ├── login/              # 登录模块
│   │   ├── auth.vue        # 微信授权
│   │   ├── openid.vue      # 获取授权
│   │   ├── login.vue       # 登录
│   │   ├── register.vue    # 注册
│   │   ├── forgetpwd.vue   # 忘记密码
│   │   └── mobilelogin.vue # 验证码登录
│   ├── address/            # 地址模块
│   │   ├── address.vue     # 地址管理
│   │   └── addedit.vue     # 添加/编辑地址
│   ├── coupon/             # 优惠券模块
│   │   ├── coupon.vue      # 优惠券列表
│   │   ├── user.vue        # 我的优惠券
│   │   └── detail.vue      # 优惠券详情
│   ├── score/              # 积分模块
│   │   ├── score.vue       # 我的积分
│   │   ├── logs.vue        # 积分日志
│   │   ├── exchange.vue    # 积分兑换
│   │   └── order.vue       # 兑换订单
│   ├── signin/             # 签到模块
│   │   ├── signin.vue      # 签到
│   │   ├── logs.vue        # 签到日志
│   │   └── ranking.vue     # 排行榜
│   ├── remark/             # 评论模块
│   │   ├── remark.vue      # 添加评论
│   │   ├── lists.vue       # 评论列表
│   │   └── comment.vue     # 我的评论
│   ├── video/              # 视频模块
│   │   └── video.vue       # 视频播放
│   ├── search/             # 搜索模块
│   │   └── search.vue      # 搜索页
│   ├── page/               # 单页模块
│   │   └── page.vue        # 单页展示
│   ├── help/               # 帮助模块
│   │   └── index.vue       # 帮助中心
│   └── webview/            # WebView模块
│       └── webview.vue     # WebView页面
├── components/             # 公共组件
│   ├── goods-item/         # 商品项组件
│   ├── order-item/         # 订单项组件
│   ├── address-item/       # 地址项组件
│   └── ...                 # 其他组件
├── common/                 # 公共资源
│   ├── api/               # API接口封装
│   │   ├── request.js     # 请求封装
│   │   ├── user.js        # 用户接口
│   │   ├── goods.js       # 商品接口
│   │   ├── order.js       # 订单接口
│   │   └── ...
│   ├── utils/             # 工具函数
│   │   ├── util.js        # 通用工具
│   │   ├── auth.js        # 认证工具
│   │   └── ...
│   └── config/            # 配置文件
│       └── config.js      # 全局配置
├── store/                  # Vuex状态管理
│   ├── index.js           # Store入口
│   ├── modules/           # 模块
│   │   ├── user.js        # 用户状态
│   │   ├── cart.js        # 购物车状态
│   │   └── ...
│   └── getters.js         # Getters
├── static/                 # 静态资源
│   ├── images/            # 图片资源
│   ├── tabbar/            # 底部导航图标
│   │   ├── home.png
│   │   ├── home-hl.png
│   │   ├── cart.png
│   │   ├── cart-hl.png
│   │   ├── my.png
│   │   └── my-hl.png
│   └── ...
├── uview-ui/              # uView UI组件库
├── uni_modules/           # uni_modules插件
├── node_modules/          # npm依赖
├── unpackage/             # 编译输出目录
├── .hbuilderx/            # HBuilderX配置
├── App.vue                # 应用入口
├── main.js                # 主入口文件
├── pages.json             # 页面配置
├── manifest.json          # 应用配置
├── uni.scss               # 全局样式变量
├── vue.config.js          # Vue配置
├── template.h5.html       # H5模板
└── package.json           # npm配置
```

## 页面配置 (pages.json)

### 页面列表

共 **38个页面**，主要包括：

1. **首页模块** (4个页面)
   - 首页、个人中心、溯源产品、溯源产品视频

2. **登录模块** (6个页面)
   - 微信授权、获取授权、登录、注册、忘记密码、验证码登录

3. **商品模块** (4个页面)
   - 商品列表、商品详情、分类、提交订单

4. **购物车模块** (1个页面)
   - 购物车

5. **订单模块** (5个页面)
   - 订单列表、订单详情、立即支付、申请售后、查看售后、查看物流

6. **地址模块** (2个页面)
   - 地址管理、添加/编辑地址

7. **优惠券模块** (3个页面)
   - 优惠券列表、我的优惠券、优惠券详情

8. **积分模块** (4个页面)
   - 我的积分、积分日志、积分兑换、兑换订单

9. **签到模块** (3个页面)
   - 签到、签到日志、排行榜

10. **评论模块** (3个页面)
    - 添加评论、评论列表、我的评论

11. **其他模块** (3个页面)
    - 视频播放、搜索、帮助中心、单页、WebView

### TabBar 配置

底部导航栏包含 3 个标签：

```json
{
  "tabBar": {
    "color": "#999999",
    "selectedColor": "#000000",
    "backgroundColor": "#FFFFFF",
    "list": [
      {
        "pagePath": "pages/index/index",
        "text": "首页"
      },
      {
        "pagePath": "pages/cart/cart",
        "text": "购物车"
      },
      {
        "pagePath": "pages/index/my",
        "text": "我的"
      }
    ]
  }
}
```

### 全局样式

```json
{
  "globalStyle": {
    "navigationBarTextStyle": "black",
    "navigationBarTitleText": "简单商城",
    "navigationBarBackgroundColor": "#F8F8F8",
    "backgroundColor": "#F8F8F8",
    "pageOrientation": "portrait"
  }
}
```

## 应用配置 (manifest.json)

### 平台配置

#### 微信小程序 (mp-weixin)
```json
{
  "appid": "wx3a708b76e9f65456",
  "setting": {
    "urlCheck": false
  },
  "permission": {
    "scope.userLocation": {
      "desc": "快速添加收货地址"
    }
  }
}
```

#### H5配置
```json
{
  "router": {
    "mode": "hash",
    "base": "./"
  },
  "title": "简单商城",
  "devServer": {
    "https": false
  }
}
```

#### App配置 (app-plus)
```json
{
  "modules": {
    "Maps": {},
    "Geolocation": {}
  },
  "distribute": {
    "android": {
      "permissions": [
        "CAMERA",
        "INTERNET",
        "ACCESS_NETWORK_STATE",
        "ACCESS_WIFI_STATE",
        "READ_PHONE_STATE",
        "WRITE_EXTERNAL_STORAGE",
        // ... 更多权限
      ]
    }
  }
}
```

## 核心功能

### 1. 用户系统

**登录方式**:
- 账号密码登录
- 手机验证码登录
- 微信授权登录 (小程序)
- 第三方登录 (QQ、微博)

**用户功能**:
- 个人资料管理
- 头像裁剪上传
- 收货地址管理
- 收藏管理
- 订单管理
- 优惠券管理
- 积分管理

### 2. 商品系统

**商品浏览**:
- 首页推荐
- 分类浏览
- 搜索功能
- 商品详情
- 商品评价

**商品信息**:
- 商品图片轮播
- SKU规格选择
- 价格展示
- 库存显示
- 销量统计

### 3. 购物车系统

**功能**:
- 添加商品到购物车
- 修改商品数量
- 删除商品
- 全选/反选
- 结算

### 4. 订单系统

**订单流程**:
```
选择商品 → 加入购物车 → 提交订单 → 选择地址
→ 选择支付方式 → 支付 → 订单完成
```

**订单状态**:
- 待付款
- 待发货
- 待收货
- 待评价
- 已完成
- 已取消
- 售后中

**订单功能**:
- 订单列表
- 订单详情
- 订单支付
- 取消订单
- 确认收货
- 申请售后
- 查看物流
- 订单评价

### 5. 支付系统

**支付方式**:
- 微信支付 (小程序内支付)
- 支付宝支付
- 余额支付

**支付流程**:
```
创建订单 → 选择支付方式 → 调起支付
→ 支付成功 → 更新订单状态
```

### 6. 优惠券系统

**优惠券类型**:
- 折扣券
- 满减券
- 代金券

**功能**:
- 领取优惠券
- 查看我的优惠券
- 使用优惠券
- 优惠券详情

### 7. 积分系统

**积分获取**:
- 签到获取
- 购物获取
- 活动获取

**积分使用**:
- 积分兑换商品
- 积分抵扣

**功能**:
- 我的积分
- 积分日志
- 积分兑换
- 兑换订单

### 8. 签到系统

**功能**:
- 每日签到
- 连续签到奖励
- 签到日志
- 签到排行榜

### 9. 评价系统

**功能**:
- 商品评价
- 评价列表
- 我的评价
- 评价图片上传

### 10. 溯源系统

**功能**:
- 溯源产品展示
- 溯源视频播放
- 产品来源信息
- 质量保证文档

## 技术实现

### API 请求封装

```javascript
// common/api/request.js
import config from '@/common/config/config.js'

export default {
  request(url, method, data) {
    return new Promise((resolve, reject) => {
      uni.request({
        url: config.baseUrl + url,
        method: method,
        data: data,
        header: {
          'content-type': 'application/json',
          'token': uni.getStorageSync('token') || ''
        },
        success: (res) => {
          if (res.data.code === 1) {
            resolve(res.data)
          } else if (res.data.code === -1) {
            // 未登录，跳转登录页
            uni.navigateTo({
              url: '/pages/login/login'
            })
            reject(res.data)
          } else {
            uni.showToast({
              title: res.data.msg,
              icon: 'none'
            })
            reject(res.data)
          }
        },
        fail: (err) => {
          uni.showToast({
            title: '网络请求失败',
            icon: 'none'
          })
          reject(err)
        }
      })
    })
  },

  get(url, data) {
    return this.request(url, 'GET', data)
  },

  post(url, data) {
    return this.request(url, 'POST', data)
  }
}
```

### Vuex 状态管理

```javascript
// store/modules/user.js
export default {
  state: {
    userInfo: null,
    token: ''
  },

  mutations: {
    SET_USER_INFO(state, userInfo) {
      state.userInfo = userInfo
      uni.setStorageSync('userInfo', userInfo)
    },

    SET_TOKEN(state, token) {
      state.token = token
      uni.setStorageSync('token', token)
    },

    LOGOUT(state) {
      state.userInfo = null
      state.token = ''
      uni.removeStorageSync('userInfo')
      uni.removeStorageSync('token')
    }
  },

  actions: {
    login({ commit }, { userInfo, token }) {
      commit('SET_USER_INFO', userInfo)
      commit('SET_TOKEN', token)
    },

    logout({ commit }) {
      commit('LOGOUT')
    }
  }
}
```

### 微信授权登录

```javascript
// 微信小程序授权登录流程
export default {
  methods: {
    async wxLogin() {
      // 1. 获取微信授权code
      const { code } = await uni.login({ provider: 'weixin' })

      // 2. 获取用户信息
      const { userInfo } = await uni.getUserProfile({
        desc: '用于完善用户资料'
      })

      // 3. 发送到后端换取token
      const res = await this.$api.post('/api/user/wxlogin', {
        code: code,
        userInfo: userInfo
      })

      // 4. 保存token和用户信息
      this.$store.dispatch('login', {
        userInfo: res.data.userInfo,
        token: res.data.token
      })

      // 5. 跳转到首页
      uni.switchTab({
        url: '/pages/index/index'
      })
    }
  }
}
```

### 支付功能

```javascript
// 微信支付
export default {
  methods: {
    async wxPay(orderId) {
      // 1. 创建支付订单
      const res = await this.$api.post('/api/order/pay', {
        order_id: orderId,
        pay_type: 'wechat'
      })

      // 2. 调起微信支付
      await uni.requestPayment({
        provider: 'wxpay',
        timeStamp: res.data.timeStamp,
        nonceStr: res.data.nonceStr,
        package: res.data.package,
        signType: res.data.signType,
        paySign: res.data.paySign
      })

      // 3. 支付成功，跳转订单详情
      uni.redirectTo({
        url: `/pages/order/detail?id=${orderId}`
      })
    }
  }
}
```

## 开发指南

### 环境要求

- HBuilderX 3.0+
- Node.js 12+
- 微信开发者工具 (小程序开发)

### 安装依赖

```bash
npm install
```

### 运行项目

#### 微信小程序
1. 使用 HBuilderX 打开项目
2. 点击 "运行" → "运行到小程序模拟器" → "微信开发者工具"
3. 在微信开发者工具中预览

#### H5
1. 点击 "运行" → "运行到浏览器" → "Chrome"
2. 浏览器自动打开预览

#### App
1. 点击 "运行" → "运行到手机或模拟器"
2. 选择设备运行

### 打包发布

#### 微信小程序
1. 点击 "发行" → "小程序-微信"
2. 填写小程序信息
3. 点击 "发行"
4. 在微信开发者工具中上传代码

#### H5
1. 点击 "发行" → "网站-H5手机版"
2. 配置发行参数
3. 点击 "发行"
4. 将 unpackage/dist/build/h5 目录部署到服务器

#### App
1. 点击 "发行" → "原生App-云打包"
2. 配置打包参数 (证书、图标等)
3. 点击 "打包"
4. 下载安装包

## 配置说明

### API 基础地址配置

```javascript
// common/config/config.js
export default {
  // 开发环境
  baseUrl: 'http://localhost/api',

  // 生产环境
  // baseUrl: 'https://your-domain.com/api',

  // 图片域名
  imageUrl: 'https://your-domain.com',

  // 超时时间
  timeout: 10000
}
```

### 微信小程序配置

1. 在 manifest.json 中配置 AppID
2. 在微信公众平台配置服务器域名
3. 配置支付参数

### 推送通知配置

```javascript
// 订阅消息模板ID
export default {
  templates: {
    orderPay: 'xxx',      // 订单支付成功
    orderShip: 'xxx',     // 订单发货通知
    orderRefund: 'xxx'    // 退款通知
  }
}
```

## 性能优化

### 图片优化
- 使用 webp 格式
- 图片懒加载
- 压缩图片大小

### 代码优化
- 分包加载 (小程序)
- 按需引入组件
- 减少页面层级

### 网络优化
- 请求合并
- 数据缓存
- 防抖节流

## 常见问题

### 1. 小程序授权失败
- 检查 AppID 是否正确
- 检查域名是否配置白名单
- 检查用户是否拒绝授权

### 2. 支付失败
- 检查支付参数是否正确
- 检查商户号配置
- 检查证书是否有效

### 3. 图片不显示
- 检查图片路径是否正确
- 检查域名是否在白名单
- 检查图片是否存在

### 4. 页面跳转失败
- 检查路径是否正确
- 检查页面是否在 pages.json 中注册
- TabBar 页面使用 switchTab 跳转

## 调试技巧

### 控制台调试
```javascript
console.log('调试信息', data)
```

### 网络请求调试
- 微信开发者工具 → Network 面板
- 查看请求参数和响应数据

### 真机调试
- 微信开发者工具 → 预览 → 扫码真机调试
- 查看真机控制台日志

## 版本更新

### v1.2.0 (当前版本)
- 新增溯源产品功能
- 新增视频播放功能
- 优化购物车性能
- 修复已知问题

### 后续计划
- 直播功能
- 拼团功能
- 分销功能
- 会员系统
