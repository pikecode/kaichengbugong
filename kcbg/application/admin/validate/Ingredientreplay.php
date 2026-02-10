<?php

namespace app\admin\validate;

use think\Validate;

class Ingredientreplay extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'title' => 'require|max:255',
        'weigh' => 'require|integer|egt:0',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'title.require' => '分类名称不能为空',
        'title.max'     => '分类名称不能超过255个字符',
        'weigh.require' => '排序不能为空',
        'weigh.integer' => '排序必须是整数',
        'weigh.egt'     => '排序必须大于等于0',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['title', 'weigh'],
        'edit' => ['title', 'weigh'],
    ];

}
