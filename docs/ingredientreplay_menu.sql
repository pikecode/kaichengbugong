-- 配料回放菜单数据
-- 插入时间：2026-02-05

-- 1. 配料回放分类菜单
INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`, `weigh`) VALUES
(320, 'ingredientreplay', '配料回放分类', 'file', 'normal', 0);

SET @ingredientreplay_id = LAST_INSERT_ID();

INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`) VALUES
(@ingredientreplay_id, 'ingredientreplay/index', '查看', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/recyclebin', '回收站', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/add', '添加', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/edit', '编辑', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/del', '删除', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/destroy', '真实删除', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/restore', '还原', 'file', 'normal'),
(@ingredientreplay_id, 'ingredientreplay/multi', '批量更新', 'file', 'normal');

-- 2. 配料回放项目菜单
INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`, `weigh`) VALUES
(320, 'ingredientreplayitem', '配料回放项目', 'file', 'normal', 0);

SET @ingredientreplayitem_id = LAST_INSERT_ID();

INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`) VALUES
(@ingredientreplayitem_id, 'ingredientreplayitem/index', '查看', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/recyclebin', '回收站', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/add', '添加', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/edit', '编辑', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/del', '删除', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/destroy', '真实删除', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/restore', '还原', 'file', 'normal'),
(@ingredientreplayitem_id, 'ingredientreplayitem/multi', '批量更新', 'file', 'normal');

-- 3. 配料回放视频菜单
INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`, `weigh`) VALUES
(320, 'ingredientreplayitemvideo', '配料回放视频管理', 'file', 'normal', 0);

SET @ingredientreplayitemvideo_id = LAST_INSERT_ID();

INSERT INTO `fa_auth_rule` (`pid`, `name`, `title`, `type`, `status`) VALUES
(@ingredientreplayitemvideo_id, 'ingredientreplayitemvideo/index', '查看', 'file', 'normal'),
(@ingredientreplayitemvideo_id, 'ingredientreplayitemvideo/add', '添加', 'file', 'normal'),
(@ingredientreplayitemvideo_id, 'ingredientreplayitemvideo/edit', '编辑', 'file', 'normal'),
(@ingredientreplayitemvideo_id, 'ingredientreplayitemvideo/del', '删除', 'file', 'normal'),
(@ingredientreplayitemvideo_id, 'ingredientreplayitemvideo/multi', '批量更新', 'file', 'normal');
