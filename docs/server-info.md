# 服务器部署信息

## 服务器基本信息

| 项目 | 信息 |
|------|------|
| **服务器IP** | <YOUR_SERVER_IP> |
| **主机名** | kcbgzhuji |
| **操作系统** | Alibaba Cloud Linux 3.2104 LTS 64位 (OpenAnolis Edition) |
| **内核版本** | 5.10.134-17.2.al8.x86_64 |
| **架构** | x86_64 |
| **SSH账号** | root |
| **SSH密码** | <YOUR_SSH_PASSWORD> |

## 硬件配置

| 资源 | 配置 |
|------|------|
| **CPU核心数** | 4核 |
| **内存总量** | 7.3 GB |
| **内存使用** | 1.1 GB |
| **磁盘总量** | 40 GB |
| **磁盘使用** | 11 GB (30%) |
| **磁盘可用** | 27 GB |

## 已安装服务

### Web服务器
- **Nginx**: v1.24.0
  - 监听端口: 80 (HTTP), 443 (HTTPS)
  - 配置目录: `/www/server/nginx/conf/`
  - 状态: ✅ 运行中

### PHP
- **已安装版本**: 7.2, 7.3, 7.4, 8.0, 8.2
- **当前使用**: PHP 7.3 (用于 cwls 项目)
- **PHP-FPM**: ✅ 运行中
- **安装目录**: `/www/server/php/`

### 数据库
- **MySQL**: v5.7.40
  - 监听端口: 3306
  - 状态: ✅ 运行中
  - Root密码: <YOUR_MYSQL_ROOT_PASSWORD>

### 管理面板
- **宝塔面板**: 已安装
  - 面板目录: `/www/server/panel/`
  - 状态: ✅ 运行中

## 网站部署情况

### 已部署网站

#### cwls.turuifanxin.cn (开成不公电商溯源系统)

| 项目 | 信息 |
|------|------|
| **域名** | wx.kcbg.com.cn |
| **网站目录** | /www/wwwroot/cwls.turuifanxin.cn |
| **入口文件** | /www/wwwroot/cwls.turuifanxin.cn/public |
| **PHP版本** | 7.3 |
| **框架** | FastAdmin + ThinkPHP 5.x |
| **SSL证书** | ✅ 已配置 |
| **网站状态** | ✅ 运行中 (HTTP 200) |

**项目结构**:
```
/www/wwwroot/cwls.turuifanxin.cn/
├── addons/          # 插件目录
├── application/     # 应用目录
├── extend/          # 扩展类库
├── public/          # 公共资源 (网站入口)
├── runtime/         # 运行时目录
├── thinkphp/        # ThinkPHP框架
└── vendor/          # Composer依赖
```

**Nginx配置**:
- 配置文件: `/www/server/panel/vhost/nginx/cwls.turuifanxin.cn.conf`
- 监听端口: 80, 443
- SSL证书路径: `/www/server/panel/vhost/cert/cwls.turuifanxin.cn/`
- 伪静态: 已配置
- PHP版本: 7.3

## 数据库信息

### cwls 数据库

| 项目 | 信息 |
|------|------|
| **数据库名** | cwls |
| **数据库用户** | cwls |
| **数据库密码** | <YOUR_DATABASE_PASSWORD> |
| **字符集** | utf8mb4 |
| **表前缀** | fa_ |
| **数据表数量** | 63张表 |

### 数据统计

| 类型 | 数量 |
|------|------|
| **用户总数** | 114 |
| **商品总数** | 39 |
| **订单总数** | 13 |

### 主要数据表

**管理员相关**:
- `fa_admin` - 管理员表
- `fa_admin_log` - 管理员日志

**商品相关**:
- `fa_shop_goods` - 商品表
- `fa_shop_category` - 分类表
- `fa_shop_brand` - 品牌表
- `fa_shop_attribute` - 属性表
- `fa_shop_carts` - 购物车表

**订单相关**:
- `fa_shop_order` - 订单表
- `fa_shop_address` - 收货地址表
- `fa_shop_coupon` - 优惠券表

**溯源相关**:
- `fa_traceability` - 溯源分类表
- `fa_traceability_goods` - 溯源商品表
- `fa_traceability_goods_video` - 溯源视频表

## 服务运行状态

| 服务 | 状态 | 端口 |
|------|------|------|
| Nginx | ✅ 运行中 | 80, 443 |
| MySQL | ✅ 运行中 | 3306 |
| PHP-FPM 7.2 | ✅ 运行中 | - |
| PHP-FPM 7.3 | ✅ 运行中 | - |
| PHP-FPM 7.4 | ✅ 运行中 | - |
| PHP-FPM 8.0 | ✅ 运行中 | - |
| PHP-FPM 8.2 | ✅ 运行中 | - |
| 宝塔面板 | ✅ 运行中 | - |

## 网站备份

**备份文件**:
- `/www/wwwroot/cwls.turuifanxin.cn_TiMrr.tar.gz` (107 MB) - 2025年7月7日
- `/www/wwwroot/cwls.turuifanxin.cn_W5kya.tar.gz` (148 MB) - 2024年12月3日

**备份目录**: `/www/backup/`

## 访问信息

### 网站访问
- **HTTP**: http://wx.kcbg.com.cn
- **HTTPS**: https://wx.kcbg.com.cn

### 后台访问
- **后台地址**: https://wx.kcbg.com.cn/xSZaYJEibq.php
- **管理员账号**: 需查询数据库 `fa_admin` 表

### 数据库访问
```bash
# 使用应用数据库用户
mysql -ucwls -p'<YOUR_DATABASE_PASSWORD>' -h127.0.0.1

# 使用root用户
mysql -uroot -p'<YOUR_MYSQL_ROOT_PASSWORD>' -h127.0.0.1
```

### SSH访问
```bash
# 使用密码登录
ssh root@<YOUR_SERVER_IP>
# 密码: <YOUR_SSH_PASSWORD>

# 或使用sshpass
sshpass -p '<YOUR_SSH_PASSWORD>' ssh root@<YOUR_SERVER_IP>
```

## 安全建议

⚠️ **重要提醒**:

1. **修改默认密码**
   - SSH root密码过于简单，建议修改
   - MySQL root密码建议定期更换
   - 后台管理员密码建议使用强密码

2. **防火墙配置**
   - 建议配置防火墙规则
   - 限制SSH登录IP
   - 关闭不必要的端口

3. **定期备份**
   - 数据库定期备份
   - 网站文件定期备份
   - 备份文件异地存储

4. **SSL证书**
   - 定期检查证书有效期
   - 及时续期SSL证书

5. **系统更新**
   - 定期更新系统补丁
   - 更新PHP、MySQL等服务
   - 更新宝塔面板

## 部署时间线

- **2024年10月15日**: 服务器初始化
- **2024年10月31日**: 宝塔面板安装
- **2024年11月10日**: cwls网站部署
- **2024年12月3日**: 网站备份
- **2025年7月7日**: 最新备份

## 监控建议

建议监控以下指标:
- CPU使用率
- 内存使用率
- 磁盘使用率
- 网站响应时间
- 数据库连接数
- Nginx访问日志
- PHP错误日志

## 日志位置

- **Nginx访问日志**: `/www/wwwlogs/cwls.turuifanxin.cn.log`
- **Nginx错误日志**: `/www/wwwlogs/cwls.turuifanxin.cn.error.log`
- **PHP错误日志**: `/www/server/php/73/var/log/php-fpm.log`
- **MySQL错误日志**: `/www/server/data/kcbgzhuji.err`

## 联系信息

- **服务器提供商**: 阿里云
- **技术支持**: 宝塔面板官方文档
- **项目仓库**: https://github.com/pikecode/kaichengbugong

---

**最后更新**: 2025年2月4日
**文档版本**: v1.0
