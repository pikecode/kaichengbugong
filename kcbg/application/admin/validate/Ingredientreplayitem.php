<?php

namespace app\admin\validate;

use think\Validate;

class Ingredientreplayitem extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'ingredientreplay_id' => 'require|integer',
        'title' => 'require|max:255',
        'weigh' => 'require|integer|egt:0',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'ingredientreplay_id.require' => '配料分类不能为空',
        'ingredientreplay_id.integer' => '配料分类ID必须是整数',
        'title.require' => '项目标题不能为空',
        'title.max'     => '项目标题不能超过255个字符',
        'weigh.require' => '排序不能为空',
        'weigh.integer' => '排序必须是整数',
        'weigh.egt'     => '排序必须大于等于0',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['ingredientreplay_id', 'title', 'weigh'],
        'edit' => ['ingredientreplay_id', 'title', 'weigh'],
    ];

}
