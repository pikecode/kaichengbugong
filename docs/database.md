# 数据库设计文档

## 数据库信息

- **数据库名**: cwls
- **字符集**: utf8mb4
- **排序规则**: utf8mb4_unicode_ci
- **表前缀**: fa_
- **数据库引擎**: InnoDB
- **MySQL版本**: 5.7+

## 表结构概览

数据库包含以下几大类表：

### 1. 管理员相关 (5张表)
- `fa_admin` - 管理员表
- `fa_admin_log` - 管理员操作日志
- `fa_auth_group` - 角色组表
- `fa_auth_rule` - 权限规则表
- `fa_auth_group_access` - 用户角色关联表

### 2. 用户相关 (10+张表)
- `fa_user` - 用户表
- `fa_user_group` - 用户组表
- `fa_user_rule` - 用户权限表
- `fa_user_score_log` - 积分日志
- `fa_user_money_log` - 余额日志
- 其他用户相关表

### 3. 商品相关 (15+张表)
- `fa_shop_goods` - 商品表
- `fa_shop_goods_sku` - SKU表
- `fa_shop_category` - 分类表
- `fa_shop_brand` - 品牌表
- 其他商品相关表

### 4. 订单相关 (10+张表)
- `fa_shop_order` - 订单表
- `fa_shop_order_goods` - 订单商品表
- `fa_shop_carts` - 购物车表
- `fa_shop_address` - 收货地址表
- 其他订单相关表

### 5. 营销相关 (5+张表)
- `fa_shop_coupon` - 优惠券表
- `fa_shop_coupon_user` - 用户优惠券表
- 其他营销相关表

### 6. 溯源相关 (3张表)
- `fa_traceability` - 溯源分类表
- `fa_traceability_goods` - 溯源商品表
- `fa_traceability_goods_video` - 溯源视频表

### 7. 系统相关 (10+张表)
- `fa_config` - 系统配置表
- `fa_attachment` - 附件表
- `fa_category` - 分类表
- `fa_area` - 地区表
- `fa_version` - 版本管理表
- 其他系统表

## 核心表结构详解

### 管理员系统

#### fa_admin (管理员表)
```sql
CREATE TABLE `fa_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) DEFAULT NULL COMMENT '登录IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
```

#### fa_auth_group (角色组表)
```sql
CREATE TABLE `fa_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色组表';
```

#### fa_auth_rule (权限规则表)
```sql
CREATE TABLE `fa_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限规则表';
```

### 用户系统

#### fa_user (用户表)
```sql
CREATE TABLE `fa_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) NOT NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) NOT NULL DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `successions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int(10) DEFAULT NULL COMMENT '上次登录时间',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) NOT NULL DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) NOT NULL DEFAULT '' COMMENT '加入IP',
  `jointime` int(10) DEFAULT NULL COMMENT '加入时间',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) NOT NULL DEFAULT '' COMMENT 'Token',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  `verification` varchar(255) NOT NULL DEFAULT '' COMMENT '验证',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
```

### 商品系统

#### fa_shop_goods (商品表)
```sql
CREATE TABLE `fa_shop_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `subtitle` varchar(255) NOT NULL DEFAULT '' COMMENT '副标题',
  `images` text NOT NULL COMMENT '商品图片',
  `description` text NOT NULL COMMENT '商品描述',
  `content` text NOT NULL COMMENT '商品详情',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `marketprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `costprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销量',
  `spec_type` enum('single','multi') NOT NULL DEFAULT 'single' COMMENT '规格类型',
  `weight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '重量(kg)',
  `volume` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '体积(m³)',
  `unit` varchar(20) NOT NULL DEFAULT '' COMMENT '单位',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `comments` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `likes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
```

#### fa_shop_goods_sku (SKU表)
```sql
CREATE TABLE `fa_shop_goods_sku` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `specs` varchar(255) NOT NULL DEFAULT '' COMMENT '规格值',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT 'SKU图片',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `marketprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `costprice` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `sales` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销量',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '商品编码',
  `weight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '重量',
  `volume` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '体积',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品SKU表';
```

### 订单系统

#### fa_shop_order (订单表)
```sql
CREATE TABLE `fa_shop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_no` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `address_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地址ID',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `payamount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `freight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费',
  `coupon_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  `coupon_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用积分',
  `score_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分抵扣金额',
  `pay_type` varchar(30) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `delivery_type` varchar(30) NOT NULL DEFAULT '' COMMENT '配送方式',
  `delivery_time` int(10) DEFAULT NULL COMMENT '发货时间',
  `finish_time` int(10) DEFAULT NULL COMMENT '完成时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` enum('created','paid','delivered','received','finished','closed','refunding','refunded') NOT NULL DEFAULT 'created' COMMENT '订单状态',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';
```

#### fa_shop_order_goods (订单商品表)
```sql
CREATE TABLE `fa_shop_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `sku_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'SKU ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品图片',
  `specs` varchar(255) NOT NULL DEFAULT '' COMMENT '规格',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单价',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '小计',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单商品表';
```

### 溯源系统

#### fa_traceability (溯源分类表)
```sql
CREATE TABLE `fa_traceability` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图片',
  `description` text NOT NULL COMMENT '分类描述',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='溯源分类表';
```

#### fa_traceability_goods (溯源商品表)
```sql
CREATE TABLE `fa_traceability_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `traceability_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '溯源分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `images` text NOT NULL COMMENT '商品图片',
  `description` text NOT NULL COMMENT '商品描述',
  `content` text NOT NULL COMMENT '详细内容',
  `origin` varchar(100) NOT NULL DEFAULT '' COMMENT '产地',
  `producer` varchar(100) NOT NULL DEFAULT '' COMMENT '生产商',
  `production_date` date DEFAULT NULL COMMENT '生产日期',
  `quality_cert` varchar(255) NOT NULL DEFAULT '' COMMENT '质量证书',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `traceability_id` (`traceability_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='溯源商品表';
```

#### fa_traceability_goods_video (溯源视频表)
```sql
CREATE TABLE `fa_traceability_goods_video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '溯源商品ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '视频标题',
  `video` varchar(255) NOT NULL DEFAULT '' COMMENT '视频地址',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '视频封面',
  `duration` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '视频时长(秒)',
  `description` text NOT NULL COMMENT '视频描述',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='溯源视频表';
```

## 索引设计

### 主键索引
所有表都有自增主键 `id`

### 唯一索引
- `fa_admin.username` - 管理员用户名
- `fa_auth_rule.name` - 权限规则名称
- `fa_shop_order.order_no` - 订单号

### 普通索引
- 外键字段 (如 `user_id`, `goods_id`, `order_id`)
- 常用查询字段 (如 `username`, `email`, `mobile`)
- 状态字段 (如 `status`)
- 时间字段 (如 `createtime`)

## 数据关系

### 用户-订单关系
```
fa_user (1) ----< (N) fa_shop_order
```

### 订单-商品关系
```
fa_shop_order (1) ----< (N) fa_shop_order_goods
fa_shop_goods (1) ----< (N) fa_shop_order_goods
```

### 商品-SKU关系
```
fa_shop_goods (1) ----< (N) fa_shop_goods_sku
```

### 溯源关系
```
fa_traceability (1) ----< (N) fa_traceability_goods
fa_traceability_goods (1) ----< (N) fa_traceability_goods_video
```

### 权限关系
```
fa_admin (N) ----< (N) fa_auth_group (通过 fa_auth_group_access)
fa_auth_group (1) ----< (N) fa_auth_rule
```

## 数据字典

### 订单状态 (order.status)
- `created` - 待付款
- `paid` - 已付款/待发货
- `delivered` - 已发货/待收货
- `received` - 已收货/待评价
- `finished` - 已完成
- `closed` - 已关闭
- `refunding` - 退款中
- `refunded` - 已退款

### 支付方式 (order.pay_type)
- `wechat` - 微信支付
- `alipay` - 支付宝
- `balance` - 余额支付

### 商品状态 (goods.status)
- `normal` - 正常
- `hidden` - 隐藏

### 用户状态 (user.status)
- `normal` - 正常
- `hidden` - 禁用

## 数据备份

### 备份策略
- 每日全量备份
- 每小时增量备份
- 保留30天备份

### 备份命令
```bash
# 全量备份
mysqldump -u root -p cwls > cwls_backup_$(date +%Y%m%d).sql

# 备份指定表
mysqldump -u root -p cwls fa_shop_order fa_shop_order_goods > order_backup.sql

# 恢复数据
mysql -u root -p cwls < cwls_backup.sql
```

## 性能优化建议

### 索引优化
- 为常用查询字段添加索引
- 避免过多索引影响写入性能
- 定期分析慢查询日志

### 表优化
- 大表考虑分表 (如订单表按月分表)
- 历史数据归档
- 定期优化表 `OPTIMIZE TABLE`

### 查询优化
- 避免 SELECT *
- 使用 LIMIT 限制结果集
- 合理使用缓存
- 避免 N+1 查询问题

## 数据迁移

### 导出数据
```bash
# 导出表结构
mysqldump -u root -p -d cwls > cwls_structure.sql

# 导出数据
mysqldump -u root -p -t cwls > cwls_data.sql

# 导出完整数据库
mysqldump -u root -p cwls > cwls_full.sql
```

### 导入数据
```bash
# 导入数据库
mysql -u root -p cwls < cwls_full.sql

# 导入指定表
mysql -u root -p cwls < table_data.sql
```

## 安全建议

### 数据安全
- 敏感数据加密存储 (密码、支付信息)
- 定期备份数据
- 限制数据库访问权限
- 使用参数化查询防止SQL注入

### 权限管理
- 最小权限原则
- 定期审计数据库权限
- 禁用不必要的数据库功能

### 监控告警
- 监控数据库性能
- 监控磁盘空间
- 异常访问告警
