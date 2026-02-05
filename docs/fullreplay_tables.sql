-- 配料回放功能数据库表结构
-- 创建时间：2026-02-05

-- 1. 配料回放分类表
CREATE TABLE `fa_ingredientreplay` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '配料分类名称',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '缩略图',
  `images` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '预览图',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '分类描述',
  `weigh` int NOT NULL DEFAULT '1' COMMENT '排序',
  `createtime` bigint unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint unsigned DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='配料回放分类';

-- 2. 配料回放项目表
CREATE TABLE `fa_ingredientreplayitem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ingredientreplay_id` int DEFAULT NULL COMMENT '配料分类id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '配料项目标题',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '缩略图',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '项目描述',
  `weigh` int NOT NULL DEFAULT '1' COMMENT '排序',
  `createtime` bigint unsigned DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint unsigned DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='配料回放项目';

-- 3. 配料回放视频表
CREATE TABLE `fa_ingredientreplayitemvideo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ingredientreplayitem_id` int DEFAULT NULL COMMENT '配料项目id',
  `video_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频路径',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '缩略图',
  `type` set('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '类型:0=完整回放,1=关键节点',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标题',
  `tag` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频标签',
  `shijian` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频时间',
  `riqi` date DEFAULT NULL COMMENT '视频日期',
  `shichang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频时长',
  `liangdian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '视频亮点',
  `status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '是否发布:0=未发布,1=发布',
  `admin_id` int DEFAULT NULL COMMENT '创建人',
  `createtime` int DEFAULT NULL COMMENT '创建时间',
  `s_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开始时间',
  `e_time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='配料回放视频';

-- 插入测试数据
INSERT INTO `fa_ingredientreplay` (`id`, `title`, `image`, `images`, `description`, `weigh`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, '原料配料', '/uploads/20250707/041491d15f0f1c94c8d2fa80843f48f9.png', '/uploads/20250707/041491d15f0f1c94c8d2fa80843f48f9.png,/uploads/20250707/ccb4fbd2ea535c2b697dce0f1ac3c76c.jpg', '原料配料过程回放', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL);

INSERT INTO `fa_ingredientreplayitem` (`id`, `ingredientreplay_id`, `title`, `image`, `description`, `weigh`, `createtime`, `updatetime`, `deletetime`) VALUES
(1, 1, '2024年1月配料记录', '/uploads/20250707/041491d15f0f1c94c8d2fa80843f48f9.png', '2024年1月份配料过程完整记录', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL);
