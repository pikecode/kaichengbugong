# 本地开发环境使用指南

## 环境要求

- Docker
- Docker Compose

## 快速开始

### 1. 启动服务

```bash
cd kcbg
docker-compose up -d
```

### 2. 安装依赖

```bash
docker-compose exec php composer install
```

### 3. 访问应用

- **前台**: http://localhost:8080
- **后台**: http://localhost:8080/xSZaYJEibq.php
- **phpMyAdmin**: http://localhost:8081

### 4. 数据库信息

- **主机**: localhost
- **端口**: 3307 (外部访问) / 3306 (容器内)
- **数据库**: cwls
- **用户名**: root
- **密码**: root123456

## 常用命令

### 查看服务状态
```bash
docker-compose ps
```

### 查看日志
```bash
docker-compose logs -f
```

### 停止服务
```bash
docker-compose stop
```

### 重启服务
```bash
docker-compose restart
```

### 删除服务和数据
```bash
docker-compose down -v
```

## 进入容器

### PHP 容器
```bash
docker-compose exec php bash
```

### MySQL 容器
```bash
docker-compose exec mysql bash
```

## 故障排查

### 端口被占用
如果 8080 或 3307 端口被占用，可以修改 `docker-compose.yml` 中的端口映射。

### 权限问题
```bash
docker-compose exec php chown -R www-data:www-data /var/www/html/runtime
docker-compose exec php chown -R www-data:www-data /var/www/html/public/uploads
```

### 清除缓存
```bash
docker-compose exec php php think clear
```
