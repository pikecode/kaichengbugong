<?php

namespace app\admin\validate;

use think\Validate;

class Ingredientreplayitemvideo extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'ingredientreplayitem_id' => 'require|integer',
        'title' => 'require|max:255',
        'video_url' => 'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'ingredientreplayitem_id.require' => '配料项目不能为空',
        'ingredientreplayitem_id.integer' => '配料项目ID必须是整数',
        'title.require' => '视频标题不能为空',
        'title.max'     => '视频标题不能超过255个字符',
        'video_url.require' => '视频路径不能为空',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['ingredientreplayitem_id', 'title', 'video_url'],
        'edit' => ['ingredientreplayitem_id', 'title', 'video_url'],
    ];

}
