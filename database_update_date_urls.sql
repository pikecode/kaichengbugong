-- 为商品表添加日期地址映射字段
-- 执行日期: 2026-02-06
-- 用途: 支持为不同日期配置不同的小程序页面地址

USE cwls;

-- 添加字段
ALTER TABLE fa_shop_goods
ADD COLUMN `date_urls` TEXT COMMENT '日期地址映射JSON，格式：{"2026-02-06":"/pages/xxx"}'
AFTER `traceability_url`;

-- 验证字段是否添加成功
SHOW COLUMNS FROM fa_shop_goods LIKE 'date_urls';
