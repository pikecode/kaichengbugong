# 开成不公电商溯源系统 - 服务管理指南

## 📋 问题总结

### 问题现象
- 小程序无法访问后端 API 接口
- 提示"网络错误"或"接口访问失败"

### 根本原因
1. **PHP 版本不匹配**
   - 系统默认 PHP 版本是 8.5.2
   - 项目基于 FastAdmin + ThinkPHP 5.x，需要 PHP 7.4
   - PHP 8.x 与项目代码存在兼容性问题

2. **服务未启动**
   - PHP 内置开发服务器没有运行
   - 小程序无法连接到后端 API

3. **路由配置**
   - 需要使用 `router.php` 来处理 FastAdmin 的路由规则
   - 直接访问会返回 404 错误

### 解决方案
- 使用 PHP 7.4 启动内置服务器
- 指定 `public/router.php` 处理路由
- 配置正确的文档根目录（public/）

---

## 🚀 快速开始

### 启动服务
```bash
cd /Users/peakom/worko/kaichengbugong
./start-server.sh
```

### 停止服务
```bash
cd /Users/peakom/worko/kaichengbugong
./stop-server.sh
```

---

## 📝 脚本说明

### start-server.sh
自动化启动脚本，包含以下功能：

1. **环境检查**
   - 检查 PHP 7.4 是否安装
   - 检查 MySQL 服务是否运行
   - 检查数据库连接是否正常

2. **端口管理**
   - 检查端口 8080 是否被占用
   - 自动停止旧的 PHP 服务

3. **服务启动**
   - 使用 PHP 7.4 启动服务器
   - 配置正确的路由处理
   - 记录进程 PID

4. **服务验证**
   - 测试 API 接口响应
   - 验证返回数据格式
   - 显示服务信息

### stop-server.sh
停止服务脚本，功能：
- 优雅停止 PHP 服务器进程
- 清理 PID 文件
- 保留日志文件供查看

---

## 🔧 环境要求

### 必需软件
- **PHP 7.4**
  ```bash
  brew install php@7.4
  ```

- **MySQL 8.0**
  ```bash
  brew install mysql@8.0
  brew services start mysql@8.0
  ```

### 项目配置
确保 `.env` 文件配置正确：
```ini
# 数据库配置
DATABASE_HOSTNAME = 127.0.0.1
DATABASE_DATABASE = cwls
DATABASE_USERNAME = root
DATABASE_PASSWORD = root123456
DATABASE_PREFIX = fa_
DATABASE_PORT = 3306
```

---

## 📍 访问地址

### 后台管理
```
http://localhost:8080/xSZaYJEibq.php
```

### API 接口
```
http://localhost:8080/index.php/addons/shop/api.common/init
```

### 小程序配置
小程序 API 基础地址配置在：
```
cwlsuniapp/common/http.interceptor.js
```

当前配置：
```javascript
let url = 'http://localhost:8080/index.php';
```

---

## 🛠️ 常用命令

### 查看服务状态
```bash
# 查看 PHP 进程
ps aux | grep 'php.*8080'

# 查看端口占用
lsof -i :8080
```

### 查看日志
```bash
# 实时查看日志
tail -f /tmp/php74-server.log

# 查看最近 100 行日志
tail -100 /tmp/php74-server.log
```

### 手动启动（不使用脚本）
```bash
cd /Users/peakom/worko/kaichengbugong/kcbg
/opt/homebrew/opt/php@7.4/bin/php -S localhost:8080 -t public/ public/router.php
```

### 手动停止
```bash
# 优雅停止
pkill -f 'php.*8080'

# 强制停止
pkill -9 -f 'php.*8080'
```

---

## ⚠️ 常见问题

### 1. 端口被占用
**现象：** 启动时提示端口 8080 已被占用

**解决：**
```bash
# 查看占用端口的进程
lsof -i :8080

# 停止占用进程
kill -9 <PID>

# 或使用停止脚本
./stop-server.sh
```

### 2. PHP 7.4 未安装
**现象：** 提示 PHP 7.4 未找到

**解决：**
```bash
# 安装 PHP 7.4
brew install php@7.4

# 验证安装
/opt/homebrew/opt/php@7.4/bin/php -v
```

### 3. MySQL 未运行
**现象：** 提示 MySQL 服务未运行

**解决：**
```bash
# 启动 MySQL
brew services start mysql@8.0

# 检查状态
brew services list | grep mysql
```

### 4. 数据库连接失败
**现象：** 提示数据库连接失败

**解决：**
1. 检查 `.env` 文件配置
2. 验证数据库密码
3. 测试连接：
   ```bash
   mysql -h127.0.0.1 -uroot -proot123456 -e "USE cwls"
   ```

### 5. API 返回 500 错误
**现象：** API 接口返回 500 Internal Server Error

**可能原因：**
- PHP 版本不正确（使用了 PHP 8.x）
- 数据库连接失败
- 代码错误

**解决：**
1. 确认使用 PHP 7.4
2. 查看错误日志：
   ```bash
   tail -f /tmp/php74-server.log
   tail -f /Users/peakom/worko/kaichengbugong/kcbg/runtime/log/$(date +%Y%m)/*.log
   ```

---

## 📊 服务监控

### 检查服务健康状态
```bash
# 测试 API 接口
curl -s http://localhost:8080/index.php/addons/shop/api.common/init | head -c 200

# 检查返回状态码
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/index.php/addons/shop/api.common/init
```

### 性能监控
```bash
# 查看 PHP 进程资源占用
ps aux | grep 'php.*8080'

# 查看 MySQL 连接数
mysql -h127.0.0.1 -uroot -proot123456 -e "SHOW STATUS LIKE 'Threads_connected'"
```

---

## 🔄 开发工作流

### 日常开发
1. 启动服务：`./start-server.sh`
2. 开发调试
3. 查看日志：`tail -f /tmp/php74-server.log`
4. 停止服务：`./stop-server.sh`

### 代码更新后
1. 停止服务：`./stop-server.sh`
2. 拉取代码：`git pull`
3. 清理缓存：`rm -rf runtime/cache/*`
4. 启动服务：`./start-server.sh`

### 数据库更新
1. 备份数据库
2. 执行 SQL 脚本
3. 重启服务：`./stop-server.sh && ./start-server.sh`

---

## 📚 相关文档

- [FastAdmin 官方文档](https://doc.fastadmin.net/)
- [ThinkPHP 5.x 文档](https://www.kancloud.cn/manual/thinkphp5)
- [uni-app 官方文档](https://uniapp.dcloud.io/)

---

## 🆘 技术支持

如遇到问题，请按以下步骤排查：

1. **查看启动日志**
   ```bash
   cat /tmp/php74-server.log
   ```

2. **查看应用日志**
   ```bash
   tail -100 /Users/peakom/worko/kaichengbugong/kcbg/runtime/log/$(date +%Y%m)/*.log
   ```

3. **检查环境**
   - PHP 版本：`/opt/homebrew/opt/php@7.4/bin/php -v`
   - MySQL 状态：`brew services list | grep mysql`
   - 端口占用：`lsof -i :8080`

4. **重启服务**
   ```bash
   ./stop-server.sh
   ./start-server.sh
   ```

---

**最后更新：** 2026年2月5日
**文档版本：** v1.0
