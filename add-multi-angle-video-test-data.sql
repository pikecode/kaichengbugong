-- 添加多视角回放测试数据
-- 使用方法：mysql -h127.0.0.1 -uroot -proot123456 cwls < add-multi-angle-video-test-data.sql

-- 插入测试视频数据（type=2 表示多视角回放）
INSERT INTO `fa_shop_video` (
    `shop_goods_id`,
    `riqi`,
    `type`,
    `tag`,
    `title`,
    `video_url`,
    `shichang`,
    `s_time`,
    `e_time`,
    `liangdian`,
    `status`,
    `createtime`
) VALUES
(
    1,
    '2026-02-05',
    2,
    '俯视角',
    '生产线俯视全景',
    'https://www.w3school.com.cn/example/html5/mov_bbb.mp4',
    '00:02:30',
    '08:00',
    '08:30',
    '从上方俯瞰整个生产线，全面展示生产流程',
    1,
    UNIX_TIMESTAMP()
),
(
    1,
    '2026-02-05',
    2,
    '侧视角',
    '关键工序侧面视角',
    'https://www.w3school.com.cn/example/html5/mov_bbb.mp4',
    '00:03:15',
    '08:30',
    '09:00',
    '侧面角度清晰展示关键操作步骤',
    1,
    UNIX_TIMESTAMP()
),
(
    1,
    '2026-02-05',
    2,
    '特写镜头',
    '精密操作特写',
    'https://www.w3school.com.cn/example/html5/mov_bbb.mp4',
    '00:01:45',
    '09:00',
    '09:30',
    '近距离特写展示精密操作细节',
    1,
    UNIX_TIMESTAMP()
);

-- 查询验证
SELECT id, shop_goods_id, riqi, type, tag, title, status
FROM fa_shop_video
WHERE type = 2
ORDER BY createtime DESC;
