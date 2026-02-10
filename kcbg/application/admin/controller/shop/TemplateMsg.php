<?php

namespace app\admin\controller\shop;

use app\common\controller\Backend;

/**
 * 模板消息
 *
 * @icon fa fa-circle-o
 */
class TemplateMsg extends Backend
{

    /**
     * TemplateMsg模型对象
     * @var \app\admin\model\shop\TemplateMsg
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shop\TemplateMsg;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("eventList", $this->model->getEventList());
        $this->assign('template_data', json_encode([
            'order_sn'     => '订单编号',
            'nickname'     => '用户昵称',
            'createtime'   => '下单时间',
            'amount'       => '订单金额',
            'discount'     => '优惠金额',
            'shippingfee'  => '配送费用',
            'saleamount'   => '应付款金额',
            'paytime'      => '支付时间',
            'shippingtime' => '发货时间',
            'receivetime'  => '收货时间',
            'diy_text'     => '自定义文本',
        ]));
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
